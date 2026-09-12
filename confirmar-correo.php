<?php
require_once __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$token = $_GET['token'] ?? '';

if ($token) {
  $pdo = conexionDB();
  $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE token_verificacion = ?");
  $stmt->execute([$token]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($usuario) {
    $pdo->prepare("UPDATE usuarios SET email_verificado = 1, token_verificacion = NULL WHERE id = ?")
        ->execute([$usuario['id']]);
    $_SESSION['usuario_id'] = $usuario['id'];
    header('Location: /membresia.php?correo-confirmado=1');
    exit;
  }
}

header('Location: /login.html?error=token-invalido');
exit;
