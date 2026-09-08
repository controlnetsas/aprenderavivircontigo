<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sincronizacion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $whatsapp = trim($_POST['whatsapp'] ?? '');
  $email = trim($_POST['correo'] ?? '');
  $taller = trim($_POST['taller'] ?? '');

  // Campo "trampa" anti-spam: si un bot lo llena, ignoramos el envío.
  if (!empty($_POST['sitio_web'] ?? '')) {
    exit;
  }

  if ($nombre && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $pdo = conexionDB();

    // Si el correo ya existe (por ejemplo, ya tiene cuenta), solo actualiza el taller de interés
    // en vez de fallar por la restricción UNIQUE de email.
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

    header('Location: /gracias.html');
    exit;
  }
}
header('Location: /contacto.html?error=1');
exit;
