<?php
// camino-accion.php — acciones de "Mi camino" (llamadas por fetch, responden JSON).
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
requiereLoginApi();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Método no permitido']); exit; }
$enviado = $_SERVER['HTTP_X_CSRF'] ?? '';
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)$enviado)) {
  http_response_code(403); echo json_encode(['error' => 'Sesión expirada, recarga la página.']); exit;
}

$pdo = conexionDB();
$usuario = usuarioActual();
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$accion = $in['accion'] ?? '';

if ($accion === 'completar') {
  $consultaId = (int)($in['consulta_id'] ?? 0);
  $ejercicioId = (int)($in['ejercicio_id'] ?? 0);
  $hecho = !empty($in['hecho']);
  $nota = isset($in['nota']) ? mb_substr(trim((string)$in['nota']), 0, 1000) : null;

  $s = $pdo->prepare(
    "SELECT c.id, c.fecha_inicio, e.dia FROM consultas c
     JOIN plantilla_ejercicios e ON e.plantilla_id = c.plantilla_id AND e.id = ?
     WHERE c.id = ? AND c.usuario_id = ? AND c.fecha_inicio IS NOT NULL"
  );
  $s->execute([$ejercicioId, $consultaId, $usuario['id']]);
  $fila = $s->fetch(PDO::FETCH_ASSOC);
  if (!$fila) { http_response_code(404); echo json_encode(['error' => 'No encontrado']); exit; }
  if ((int)$fila['dia'] > diaActual($fila['fecha_inicio'])) {
    http_response_code(400); echo json_encode(['error' => 'Este ejercicio se abre en su día.']); exit;
  }

  if ($hecho) {
    $pdo->prepare(
      "INSERT INTO progreso_ejercicios (consulta_id, ejercicio_id, nota) VALUES (?,?,?)
       ON DUPLICATE KEY UPDATE nota = VALUES(nota)"
    )->execute([$consultaId, $ejercicioId, $nota ?: null]);
  } else {
    $pdo->prepare("DELETE FROM progreso_ejercicios WHERE consulta_id = ? AND ejercicio_id = ?")->execute([$consultaId, $ejercicioId]);
  }
  $t = $pdo->prepare("SELECT COUNT(*) FROM plantilla_ejercicios WHERE plantilla_id = (SELECT plantilla_id FROM consultas WHERE id = ?)");
  $t->execute([$consultaId]);
  $h = $pdo->prepare("SELECT COUNT(*) FROM progreso_ejercicios WHERE consulta_id = ?");
  $h->execute([$consultaId]);
  echo json_encode(['ok' => true, 'hechos' => (int)$h->fetchColumn(), 'total' => (int)$t->fetchColumn()]);
  exit;
}

if ($accion === 'interes') {
  $taller = $in['taller'] ?? '';
  if (!isset(talleresCatalogo()[$taller])) { http_response_code(400); echo json_encode(['error' => 'Taller inválido']); exit; }
  // Solo sube de "recomendado"/sin registro a "interesado"; no baja inscrito ni completado.
  $pdo->prepare(
    "INSERT INTO consultante_talleres (usuario_id, taller, estado) VALUES (?,?, 'interesado')
     ON DUPLICATE KEY UPDATE estado = IF(estado = 'recomendado', 'interesado', estado)"
  )->execute([$usuario['id'], $taller]);
  echo json_encode(['ok' => true]);
  exit;
}

http_response_code(400);
echo json_encode(['error' => 'Acción desconocida']);
