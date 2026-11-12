<?php
// consultante.php — ficha viva del consultante para el terapeuta: datos, informes, tests y notas de seguimiento.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/ficha-campos.php';
$terapeuta = requiereTerapeuta();
$pdo = conexionDB();

$s = $pdo->prepare("SELECT e.*, u.nombre, u.email, u.telefono FROM evaluaciones e JOIN usuarios u ON u.id = e.usuario_id WHERE e.id = ? AND e.terapeuta_usuario_id = ?");
$s->execute([(int)($_GET['id'] ?? 0), $terapeuta['id']]);
$ev = $s->fetch(PDO::FETCH_ASSOC);
if (!$ev) { http_response_code(404); paginaInicio('No encontrado'); echo '<div class="card"><p>No encontramos ese consultante. <a href="/evaluaciones.php">Volver</a></p></div>'; paginaFin(); exit; }
$uid = (int)$ev['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  if (($_POST['accion'] ?? '') === 'nota') {
    $nota = trim($_POST['nota'] ?? '');
    if ($nota !== '') {
      try { $pdo->prepare("INSERT INTO notas_seguimiento (usuario_id, terapeuta_usuario_id, nota) VALUES (?,?,?)")->execute([$uid, $terapeuta['id'], mb_substr($nota, 0, 8000)]); }
      catch (Throwable $e) { error_log('notas: ' . $e->getMessage()); }
    }
  }
  header('Location: /consultante.php?id=' . (int)$ev['id'] . '#notas'); exit;
}

$notas = $versiones = $resultados = [];
try { $q = $pdo->prepare("SELECT nota, creado_en FROM notas_seguimiento WHERE usuario_id = ? ORDER BY id DESC LIMIT 200"); $q->execute([$uid]); $notas = $q->fetchAll(PDO::FETCH_ASSOC); } catch (Throwable $e) {}
try { $q = $pdo->prepare("SELECT id, creado_en FROM informes_evaluacion WHERE usuario_id = ? ORDER BY id DESC LIMIT 30"); $q->execute([$uid]); $versiones = $q->fetchAll(PDO::FETCH_ASSOC); } catch (Throwable $e) {}
try { $q = $pdo->prepare("SELECT test, resumen, creado_en FROM resultados_tests WHERE usuario_id = ? ORDER BY id DESC LIMIT 60"); $q->execute([$uid]); $resultados = $q->fetchAll(PDO::FETCH_ASSOC); } catch (Throwable $e) {}
$q = $pdo->prepare("SELECT id, fecha_consulta, estado, notas_privadas, mensaje_terapeuta FROM consultas WHERE usuario_id = ? OR email = ? ORDER BY id DESC");
$q->execute([$uid, $ev['email']]);
$consultas = $q->fetchAll(PDO::FETCH_ASSOC);

$opC = opcionesConflictoFicha();
$conf = array_filter(explode(',', (string)$ev['conflictos_emocionales']));
$det = json_decode((string)$ev['conflicto_descripcion'], true) ?: [];
$msgWa = 'Hola ' . primerNombre($ev['nombre']) . ', soy Carlos de Aprender a Vivir Contigo. ';

paginaInicio('Consultante: ' . $ev['nombre']);
?>
<p class="mut"><a href="/evaluaciones.php">← Evaluaciones</a></p>
<div class="hero">
  <h1><?= h($ev['nombre']) ?></h1>
  <p><?= h($ev['email']) ?> · Cel. <?= h($ev['telefono']) ?> · Registrada el <?= h(substr((string)$ev['creado_en'], 0, 10)) ?></p>
  <p><span class="chip"><?= h($ev['estado'] === 'completada' ? 'Evaluación completada' : 'Evaluación ' . $ev['estado']) ?></span></p>
  <p class="fila" style="margin-top:12px">
    <?php if ($ev['telefono']): ?><a class="btn btn-primary" target="_blank" rel="noopener" href="<?= h(linkWhatsApp($ev['telefono'], $msgWa)) ?>">Escribirle por WhatsApp</a><?php endif; ?>
    <?php if ($ev['estado'] === 'completada'): ?><a class="btn btn-outline" style="color:#fff;border-color:#fff" href="/informe.php?id=<?= (int)$ev['id'] ?>">Ver último informe</a><?php endif; ?>
  </p>
</div>

<div class="card" id="notas" style="margin-bottom:16px">
  <h2 style="margin-top:0">Notas de seguimiento</h2>
  <p class="mut">Tu bitácora privada por sesión o contacto. La persona no la ve.</p>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="nota">
    <textarea class="campo" name="nota" rows="4" maxlength="8000" placeholder="Qué trabajaron hoy, lo que observas, tareas para la próxima sesión…" required></textarea>
    <p><button class="btn btn-primary" type="submit">Agregar nota</button></p>
  </form>
  <?php foreach ($notas as $n): ?>
    <div style="border-top:1px solid #eadfc9;padding:10px 0"><span class="mut"><?= h($n['creado_en']) ?></span><br><?= nl2br(h($n['nota'])) ?></div>
  <?php endforeach; if (!$notas) echo '<p class="mut">Aún no hay notas.</p>'; ?>
</div>

<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Ficha actual</h2>
  <table class="tabla">
    <?php foreach (['Edad' => $ev['edad'], 'Sexo' => $ev['sexo'], 'Síntomas' => $ev['sintomas'], 'Situación sentimental' => $ev['situacion_sentimental'], 'Hijos y nombres' => $ev['hijos'],
      'Hijos no nacidos o muertos al nacer' => $ev['hijos_no_nacidos'], 'Estado de salud' => $ev['estado_salud'], 'Trabajo actual' => $ev['trabajo_actual'], 'Profesión' => $ev['profesion']] as $k => $v): ?>
      <tr><th style="width:36%"><?= h($k) ?></th><td><?= ($v === null || $v === '') ? '<span class="mut">—</span>' : nl2br(h($v)) ?></td></tr>
    <?php endforeach; ?>
  </table>
  <h3>Conflictos emocionales sin resolver</h3>
  <?php if ($conf): ?><ul><?php foreach ($conf as $k): ?><li><strong><?= h($opC[$k] ?? $k) ?></strong><?= !empty($det[$k]) ? '<br>' . nl2br(h($det[$k])) : '' ?></li><?php endforeach; ?></ul>
  <?php else: ?><p class="mut">No marcó ninguno.</p><?php endif; ?>
  <p class="mut">La persona puede actualizar su ficha cuando quiera desde "Mi evaluación"; aquí siempre ves la versión más reciente.</p>
</div>

<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Historial de tests e informes</h2>
  <?php if ($versiones): ?><p>Informes generados: <?php foreach ($versiones as $v): ?><a href="/informe.php?id=<?= (int)$ev['id'] ?>&v=<?= (int)$v['id'] ?>"><?= h($v['creado_en']) ?></a> · <?php endforeach; ?></p><?php endif; ?>
  <?php if (!$resultados): ?><p class="mut">Aún no ha guardado tests.</p><?php else: ?>
  <table class="tabla"><?php foreach ($resultados as $r): ?>
    <tr><td style="width:22%" class="mut"><?= h(substr($r['creado_en'], 0, 16)) ?></td><td><span class="chip"><?= h(testsCatalogo()[$r['test']]['nombre'] ?? $r['test']) ?></span><br><?= h($r['resumen']) ?></td></tr>
  <?php endforeach; ?></table><?php endif; ?>
</div>

<div class="card">
  <h2 style="margin-top:0">Consultas y plan de acompañamiento</h2>
  <?php if (!$consultas): ?><p class="mut">Sin consultas registradas con este correo. Créala en el <a href="/terapeuta.php">panel del terapeuta</a> usando el mismo correo y quedará enlazada aquí.</p>
  <?php else: ?><table class="tabla"><?php foreach ($consultas as $c): ?>
    <tr><td class="mut" style="width:22%"><?= h($c['fecha_consulta']) ?></td><td><span class="chip"><?= h($c['estado']) ?></span> <?= !empty($c['notas_privadas']) ? '<br>' . nl2br(h($c['notas_privadas'])) : '' ?></td></tr>
  <?php endforeach; ?></table><?php endif; ?>
</div>
<?php paginaFin();
