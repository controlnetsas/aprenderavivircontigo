<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sincronizacion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false]);
  exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$email = trim($_POST['correo'] ?? '');
$taller = trim($_POST['taller'] ?? '');

// Campo trampa anti-spam: si viene lleno, ignoramos el envío silenciosamente.
if (!empty($_POST['sitio_web'] ?? '')) {
  echo json_encode(['ok' => true]);
  exit;
}

if (!$nombre || !$whatsapp) {
  http_response_code(400);
  echo json_encode(['ok' => false]);
  exit;
}

$pdo = conexionDB();

// El correo es opcional en este formulario (a diferencia del registro de cuenta),
// así que solo se guarda/sincroniza en HubSpot y Brevo si la persona lo dejó.
if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $existe = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
  $existe->execute([$email]);
  $usuarioExistente = $existe->fetch(PDO::FETCH_ASSOC);

  if ($usuarioExistente) {
    $pdo->prepare("UPDATE usuarios SET telefono = ?, taller_interes = ? WHERE id = ?")
        ->execute([$whatsapp, $taller, $usuarioExistente['id']]);
  } else {
    $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, taller_interes) VALUES (?, ?, ?, ?)")
        ->execute([$nombre, $email, $whatsapp, $taller]);
  }

  sincronizarContactoHubSpot($nombre, $email);
  sincronizarContactoBrevo($nombre, $email, $whatsapp, $taller);
}

echo json_encode(['ok' => true]);
