<?php
// informe.php — ver el informe de una evaluación (terapeuta responsable o la propia persona).
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
requiereLogin();
$pdo = conexionDB();
$usuario = usuarioActual();
$s = $pdo->prepare("SELECT e.*, u.nombre AS persona FROM evaluaciones e JOIN usuarios u ON u.id = e.usuario_id WHERE e.id = ?");
$s->execute([(int)($_GET['id'] ?? 0)]);
$ev = $s->fetch(PDO::FETCH_ASSOC);
$permitido = $ev && $usuario && ((int)$ev['usuario_id'] === (int)$usuario['id'] || (($usuario['rol'] ?? '') === 'terapeuta'));
if (!$permitido) { http_response_code(404); paginaInicio('No encontrado'); echo '<div class="card"><p>No encontramos ese informe.</p></div>'; paginaFin(); exit; }
paginaInicio('Informe de ' . $ev['persona']);
echo '<p class="mut">' . (($usuario['rol'] ?? '') === 'terapeuta' ? '<a href="/evaluaciones.php">← Evaluaciones</a>' : '<a href="/mi-evaluacion.php">← Mi evaluación</a>') . ' · <a href="#" onclick="window.print();return false">Imprimir / guardar PDF</a></p>';
$versiones = [];
try {
  $vs = $pdo->prepare("SELECT id, creado_en FROM informes_evaluacion WHERE evaluacion_id = ? ORDER BY id DESC LIMIT 30");
  $vs->execute([$ev['id']]);
  $versiones = $vs->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {}
$html = $ev['informe'];
if (!empty($_GET['v'])) {
  foreach ($versiones as $v) if ((int)$v['id'] === (int)$_GET['v']) {
    $q = $pdo->prepare("SELECT informe FROM informes_evaluacion WHERE id = ?"); $q->execute([$v['id']]); $html = $q->fetchColumn(); }
}
echo '<div class="card">' . ($html ?: '<p>El informe aún no se ha generado: la persona no ha terminado su evaluación.</p>') . '</div>';
if (count($versiones) > 1) {
  echo '<p class="mut no-imprimir" style="margin-top:14px">Versiones guardadas: ';
  foreach ($versiones as $v) echo '<a href="?id=' . (int)$ev['id'] . '&v=' . (int)$v['id'] . '">' . h($v['creado_en']) . '</a> · ';
  echo '</p>';
}
if (($usuario['rol'] ?? '') === 'terapeuta') echo '<p class="no-imprimir"><a class="btn btn-outline-dark" href="/consultante.php?id=' . (int)$ev['id'] . '">Ver ficha y seguimiento del consultante</a></p>';
paginaFin();
