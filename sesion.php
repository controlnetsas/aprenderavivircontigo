<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function usuarioActual() {
  if (empty($_SESSION['usuario_id'])) return null;
  $stmt = conexionDB()->prepare(
    "SELECT id, nombre, email, estado_membresia, fecha_vencimiento, rol FROM usuarios WHERE id = ?"
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

// Solo el terapeuta entra a su panel; cualquier otro usuario vuelve a la membresía.
function requiereTerapeuta() {
  requiereLogin();
  $u = usuarioActual();
  if (!$u || ($u['rol'] ?? '') !== 'terapeuta') {
    header('Location: /membresia.php');
    exit;
  }
  return $u;
}

// Token CSRF para formularios y llamadas POST de usuarios con sesión.
function tokenCsrf() {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));
  return $_SESSION['csrf'];
}

function verificaCsrf() {
  $enviado = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? '');
  if (empty($_SESSION['csrf']) || !is_string($enviado) || !hash_equals($_SESSION['csrf'], $enviado)) {
    http_response_code(403);
    exit('Sesión expirada. Recarga la página e intenta de nuevo.');
  }
}
