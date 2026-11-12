<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['correo'] ?? '');
  $password = $_POST['password'] ?? '';

  $stmt = conexionDB()->prepare("SELECT id, password_hash, email_verificado FROM usuarios WHERE email = ?");
  $stmt->execute([$email]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($usuario && $usuario['password_hash'] && password_verify($password, $usuario['password_hash'])) {
    if ((int)$usuario['email_verificado'] !== 1) {
      header('Location: /login.html?error=no-verificado');
      exit;
    }
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['usuario_id'] = $usuario['id'];
    $destino = '/membresia.php';
    try {
      $ev = conexionDB()->prepare("SELECT estado FROM evaluaciones WHERE usuario_id = ?");
      $ev->execute([$usuario['id']]);
      $estadoEv = $ev->fetchColumn();
      if ($estadoEv !== false && $estadoEv !== 'completada') $destino = '/mi-evaluacion.php';
    } catch (Throwable $e) { /* sin tabla de evaluaciones */ }
    header('Location: ' . $destino);
    exit;
  }
}
header('Location: /login.html?error=1');
exit;
