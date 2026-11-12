<?php
// diagnostico.php — TEMPORAL. Muestra qué falla en el servidor sin revelar claves. BÓRRALO al terminar.
if (($_GET['k'] ?? '') !== 'avc2026') { http_response_code(404); exit; }
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "PHP: " . PHP_VERSION . "\n";
echo "config.php en public_html: " . (file_exists(__DIR__ . '/config.php') ? 'SI' : 'NO') . "\n";
echo "config.php un nivel arriba: " . (file_exists(__DIR__ . '/../config.php') ? 'SI' : 'NO') . "\n";

foreach (['db.php', 'sesion.php', 'sincronizacion.php', 'correos.php', 'camino-nucleo.php', 'login.php', 'terapeuta.php'] as $f) {
  echo "$f existe: " . (file_exists(__DIR__ . "/$f") ? 'SI' : 'NO') . "\n";
}

try {
  require_once file_exists(__DIR__ . '/../config.php') ? __DIR__ . '/../config.php' : __DIR__ . '/config.php';
  foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'HUBSPOT_PRIVATE_APP_TOKEN', 'BREVO_API_KEY'] as $c) {
    echo "$c definida: " . (defined($c) && constant($c) !== '' ? 'SI' : 'NO') . "\n";
  }
} catch (Throwable $e) {
  echo "ERROR en config.php: " . $e->getMessage() . "\n";
}

try {
  require_once __DIR__ . '/db.php';
  $pdo = conexionDB();
  echo "Conexion a la base: OK\n";
  $cols = $pdo->query("SHOW COLUMNS FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);
  echo "Columnas de usuarios: " . implode(', ', $cols) . "\n";
  $tablas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
  echo "Tablas: " . implode(', ', $tablas) . "\n";
  $n = $pdo->query("SELECT email, email_verificado, rol FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
  foreach ($n as $u) echo "Usuario: {$u['email']} verificado={$u['email_verificado']} rol={$u['rol']}\n";
} catch (Throwable $e) {
  echo "ERROR de base de datos: " . get_class($e) . ": " . $e->getMessage() . "\n";
}

echo "\nFin. Borra este archivo del servidor.\n";
