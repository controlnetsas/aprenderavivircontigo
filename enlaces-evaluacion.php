<?php
// enlaces-evaluacion.php — enlaces de evaluación temporales: vigencia, usos y estado.
// Las fechas se manejan en hora de Colombia, calculadas en PHP (no depende de la hora del servidor MySQL).
require_once __DIR__ . '/db.php';

function ahoraBogota($sumar = '') {
  $d = new DateTimeImmutable('now', new DateTimeZone('America/Bogota'));
  if ($sumar !== '') $d = $d->modify($sumar);
  return $d->format('Y-m-d H:i:s');
}

// Enlace utilizable ahora: activo, no vencido y con usos disponibles. Devuelve la fila o null.
function enlaceVigente($pdo, $token) {
  if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return null;
  try {
    $s = $pdo->prepare("SELECT * FROM enlaces_evaluacion WHERE token = ? AND activo = 1");
    $s->execute([$token]);
    $e = $s->fetch(PDO::FETCH_ASSOC);
  } catch (Throwable $ex) { return null; }
  if (!$e) return null;
  return estadoEnlace($e) === 'activo' ? $e : null;
}

// 'activo' | 'vencido' | 'usado' | 'revocado'
function estadoEnlace($e) {
  if ((int)$e['activo'] !== 1) return 'revocado';
  if (!empty($e['expira_en']) && $e['expira_en'] <= ahoraBogota()) return 'vencido';
  if ((int)($e['usos'] ?? 0) >= (int)($e['usos_max'] ?? 1)) return 'usado';
  return 'activo';
}

// Gasta un uso del enlace. Devuelve false si ya no quedan usos o venció (evita registros simultáneos de más).
function consumirEnlace($pdo, $enlaceId) {
  $s = $pdo->prepare("UPDATE enlaces_evaluacion SET usos = usos + 1
                      WHERE id = ? AND activo = 1 AND usos < usos_max AND (expira_en IS NULL OR expira_en > ?)");
  $s->execute([$enlaceId, ahoraBogota()]);
  return $s->rowCount() === 1;
}

function textoVence($expira) {
  if (empty($expira)) return 'sin fecha';
  $dif = strtotime($expira) - strtotime(ahoraBogota());
  if ($dif <= 0) return 'venció ' . date('d/m H:i', strtotime($expira));
  if ($dif < 3600) return 'en ' . max(1, (int)round($dif / 60)) . ' min';
  if ($dif < 86400) return 'en ' . (int)floor($dif / 3600) . ' h (' . date('d/m H:i', strtotime($expira)) . ')';
  return 'en ' . (int)floor($dif / 86400) . ' d (' . date('d/m H:i', strtotime($expira)) . ')';
}

// Regla: quien se registra con un enlace del terapeuta recibe acceso al Club de Membresía por este número de meses.
// Pasado ese tiempo, la membresía vence sola (sesion.php/esMiembroActivo) y se le invita a comprarla o a pedir una consulta.
if (!defined('MESES_ACCESO_CONSULTANTE')) define('MESES_ACCESO_CONSULTANTE', 3);

function otorgarAccesoConsultante($pdo, $usuarioId) {
  $hasta = date('Y-m-d', strtotime(ahoraBogota('+' . MESES_ACCESO_CONSULTANTE . ' months'))) . ' 23:59:59';
  $u = $pdo->prepare("SELECT estado_membresia, fecha_vencimiento FROM usuarios WHERE id = ?");
  $u->execute([$usuarioId]);
  $f = $u->fetch(PDO::FETCH_ASSOC);
  if (!$f) return;
  // Si ya tiene una membresía activa que dura más, no se le acorta.
  if ($f['estado_membresia'] === 'activa' && !empty($f['fecha_vencimiento']) && $f['fecha_vencimiento'] >= $hasta) return;
  $pdo->prepare("UPDATE usuarios SET estado_membresia = 'activa', fecha_inicio_membresia = ?, fecha_vencimiento = ? WHERE id = ?")
      ->execute([ahoraBogota(), $hasta, $usuarioId]);
}

// Crea la evaluación de una persona con los tests que el terapeuta eligió en el enlace.
function crearEvaluacion($pdo, $usuarioId, $enlace) {
  $tests = !empty($enlace['tests']) ? $enlace['tests'] : 'genealogia,eneatipo,herida';
  try {
    $pdo->prepare("INSERT INTO evaluaciones (usuario_id, terapeuta_usuario_id, enlace_id, consentimiento_en, tests) VALUES (?,?,?,NOW(),?)")
        ->execute([$usuarioId, $enlace['terapeuta_usuario_id'], $enlace['id'], $tests]);
  } catch (PDOException $e) {
    // Si aún no se corrió migracion-tests-eval.sql, se crea sin la columna `tests` (quedan las tres de siempre).
    $pdo->prepare("INSERT INTO evaluaciones (usuario_id, terapeuta_usuario_id, enlace_id, consentimiento_en) VALUES (?,?,?,NOW())")
        ->execute([$usuarioId, $enlace['terapeuta_usuario_id'], $enlace['id']]);
  }
}
