<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sincronizacion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['correo'] ?? '');
  $telefono = trim($_POST['whatsapp'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!empty($_POST['sitio_web'] ?? '')) exit; // campo trampa anti-spam

  if ($nombre && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 8) {
    $pdo = conexionDB();

    $existe = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $existe->execute([$email]);
    if ($existe->fetch()) {
      header('Location: /login.html?error=1');
      exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
      "INSERT INTO usuarios (nombre, email, telefono, password_hash, estado_membresia)
       VALUES (?, ?, ?, ?, 'pendiente')"
    );
    $stmt->execute([$nombre, $email, $telefono, $hash]);

    sincronizarContactoHubSpot($nombre, $email);
    sincronizarContactoBrevo($nombre, $email, $telefono, 'Boletín general');

    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['usuario_id'] = $pdo->lastInsertId();
    header('Location: /membresia.php');
    exit;
  }
}
header('Location: /login.html?error=1');
exit;
