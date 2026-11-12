<?php
// diagnostico.php — TEMPORAL. Muestra el error real de un archivo. BÓRRALO al terminar.
if (($_GET['k'] ?? '') !== 'avc2026') { http_response_code(404); exit; }
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1'); error_reporting(E_ALL);
register_shutdown_function(function () {
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) echo "\nERROR FATAL: {$e['message']} en {$e['file']}:{$e['line']}\n";
});
echo "PHP " . PHP_VERSION . "\n";
foreach (['db.php', 'sincronizacion.php', 'correos.php', 'registro-cuenta.php', 'login.php', 'sesion.php', 'camino-nucleo.php'] as $f) {
  echo str_pad($f, 24) . (file_exists(__DIR__ . "/$f") ? filesize(__DIR__ . "/$f") . " bytes, " . date('Y-m-d H:i', filemtime(__DIR__ . "/$f")) : 'NO EXISTE') . "\n";
}
echo "\nCargando archivos uno por uno:\n";
foreach (['db.php', 'correos.php', 'sincronizacion.php', 'sesion.php', 'camino-nucleo.php'] as $f) {
  try { require_once __DIR__ . "/$f"; echo "OK  $f\n"; }
  catch (Throwable $t) { echo "FALLA $f: " . $t->getMessage() . " en " . basename($t->getFile()) . ":" . $t->getLine() . "\n"; }
}
echo "\nSMTP configurado: " . (defined('SMTP_USER') && SMTP_USER ? 'SI' : 'NO') . "\n";
echo "Fin. Borra este archivo del servidor.\n";
