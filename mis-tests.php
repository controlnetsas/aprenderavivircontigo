<?php
// mis-tests.php — lista de los tests que la persona puede hacer.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
requiereLogin();
$pdo = conexionDB();
$usuario = usuarioActual();
if (!$usuario) { header('Location: /login.html'); exit; }
$permitidos = testsPermitidos($pdo, $usuario);

paginaInicio('Mis tests');
echo '<p class="mut"><a href="/mi-camino.php">← Mi camino</a> · <a href="/membresia.php">Mi membresía</a></p>';
echo '<h1>Mis tests</h1>';
if (!$permitidos) {
  echo '<div class="card"><p>Por ahora no tienes tests disponibles. Se habilitan para miembros del club, o cuando Carlos te los envía después de tu consulta.</p>'
     . '<p><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, quiero hacer los tests de autoindagación.') . '">Escribir por WhatsApp</a></p></div>';
  paginaFin();
  exit;
}
echo '<p class="mut">Son guías de autoobservación, no diagnósticos. Puedes hacerlos a tu ritmo.</p>';
foreach (testsCatalogo() as $clave => $t) {
  if (!in_array($clave, $permitidos, true)) continue;
  echo '<div class="card" style="margin-bottom:14px"><h3 class="mt-0">' . h($t['nombre']) . '</h3><p>' . h($t['resumen']) . '</p>'
     . '<a class="btn btn-primary" href="' . h($t['pagina']) . '">Hacer el test</a></div>';
}
paginaFin();
