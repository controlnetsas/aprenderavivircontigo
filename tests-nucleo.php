<?php
// tests-nucleo.php — estructura común de las páginas de tests privados.
function paginaTestInicio($clave, $cat, $intro) {
  paginaInicio($cat['nombre']);
  echo '<style>.wrap{max-width:980px}</style>';
  echo '<p class="mut"><a href="/mis-tests.php">← Mis tests</a> · <a href="/mi-camino.php">Mi camino</a></p>';
  echo '<h1>' . h($cat['nombre']) . '</h1>';
  echo '<div class="disclaimer" style="max-width:100%;">' . h($intro)
     . ' Tus respuestas se escriben aquí. Solo se guardan en tu proceso si tú lo decides al final, con el botón "Guardar mi resultado".</div>';
}

function paginaTestFin($clave, $scripts) {
  $csrf = tokenCsrf();
  echo '<div id="guardar-panel" style="margin-top:36px"></div>';
  echo '<script>window.AVC_TEST_CLAVE=' . json_encode($clave) . ';window.AVC_CSRF=' . json_encode($csrf) . ';</script>';
  foreach ($scripts as $s) echo '<script src="/assets/js/' . h($s) . '.js"></script>';
  echo '<script src="/assets/js/guardar-test.js"></script>';
  paginaFin();
}
