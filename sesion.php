<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function usuarioActual() {
  if (empty($_SESSION['usuario_id'])) return null;
  $stmt = conexionDB()->prepare(
    "SELECT id, nombre, email, estado_membresia, fecha_vencimiento FROM usuarios WHERE id = ?"
  );
  $stmt->execute([$_SESSION['usuario_id']]);
  return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Para páginas HTML: redirige a login.html si no hay sesión.
function requiereLogin() {
  if (empty($_SESSION['usuario_id'])) {
    header('Location: /login.html');
    exit;
  }
}

// Para endpoints llamados por JavaScript (fetch): responde 401 en JSON en vez de redirigir.
function requiereLoginApi() {
  if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autenticado']);
    exit;
  }
}

function esMiembroActivo($usuario) {
  return $usuario
    && $usuario['estado_membresia'] === 'activa'
    && (!$usuario['fecha_vencimiento'] || strtotime($usuario['fecha_vencimiento']) >= time());
}
