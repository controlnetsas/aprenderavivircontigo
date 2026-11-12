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

// ---- Prueba de envío (solo si se pide): diagnostico.php?k=avc2026&enviar=correo@ejemplo.com ----
if (!empty($_GET['enviar']) && filter_var($_GET['enviar'], FILTER_VALIDATE_EMAIL)) {
  $dest = $_GET['enviar'];
  echo "\n=== PRUEBA SMTP hacia $dest ===\n";
  $host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.hostinger.com';
  $puerto = defined('SMTP_PORT') ? (int)SMTP_PORT : 465;
  echo "Conectando a $host:$puerto ...\n";
  $f = @stream_socket_client('ssl://' . $host . ':' . $puerto, $en, $es, 15);
  if (!$f) { echo "NO CONECTA: $es ($en)\n"; }
  else {
    stream_set_timeout($f, 15);
    $leer = function () use ($f) { $r = ''; while (($l = fgets($f, 515)) !== false) { $r .= $l; if (strlen($l) < 4 || $l[3] === ' ') break; } return trim($r); };
    $paso = function ($txt, $c, $oculto = false) use ($f, $leer) {
      if ($c !== null) fwrite($f, $c . "\r\n");
      $r = $leer(); echo ($txt ? "[$txt] " : '') . $r . "\n"; return $r;
    };
    $paso('saludo', null);
    $paso('EHLO', 'EHLO aprenderavivircontigo.com');
    $paso('AUTH', 'AUTH LOGIN');
    $paso('usuario', base64_encode(SMTP_USER));
    $r = $paso('clave', base64_encode(SMTP_PASS));
    if (strpos($r, '235') === 0) {
      $paso('MAIL FROM', 'MAIL FROM:<' . SMTP_USER . '>');
      $paso('RCPT TO', 'RCPT TO:<' . $dest . '>');
      $paso('DATA', 'DATA');
      $msg = "From: Aprender a Vivir Contigo <" . SMTP_USER . ">\r\nTo: <$dest>\r\nSubject: Prueba de correo\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\nSi lees esto, el envio funciona.\r\n.\r\n";
      fwrite($f, $msg); echo "[envio] " . $leer() . "\n";
    } else echo "LA AUTENTICACION FALLO: revisa SMTP_USER y SMTP_PASS en config.php\n";
    fwrite($f, "QUIT\r\n"); fclose($f);
  }
  echo "\nmail() nativo: " . (function_exists('mail') ? 'disponible' : 'NO disponible') . "\n";
}
