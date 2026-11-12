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
    // Si venía de un enlace de evaluación, regresa ahí (solo se acepta ese formato exacto).
    $next = $_POST['next'] ?? '';
    if (is_string($next) && preg_match('#^/evaluacion\.php\?t=[a-f0-9]{64}$#', $next)) $destino = $next;
    header('Location: ' . $destino);
    exit;
  }
}
header('Location: /login.html?error=1');
exit;
