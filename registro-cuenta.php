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

    $existe = $pdo->prepare("SELECT id, email_verificado FROM usuarios WHERE email = ?");
    $existe->execute([$email]);
    $usuarioExistente = $existe->fetch(PDO::FETCH_ASSOC);

    if ($usuarioExistente) {
      // Ya existe una cuenta con ese correo — lo mandamos a iniciar sesión en vez de duplicarla.
      header('Location: /login.html?error=correo-existente');
      exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare(
      "INSERT INTO usuarios (nombre, email, telefono, password_hash, estado_membresia, email_verificado, token_verificacion)
       VALUES (?, ?, ?, ?, 'pendiente', 0, ?)"
    );
    $stmt->execute([$nombre, $email, $telefono, $hash, $token]);

    sincronizarContactoHubSpot($nombre, $email);
    sincronizarContactoBrevo($nombre, $email, $telefono, 'Boletín general');

    $linkConfirmacion = 'https://' . $_SERVER['HTTP_HOST'] . '/confirmar-correo.php?token=' . $token;
    enviarCorreoConfirmacion($nombre, $email, $linkConfirmacion);

    header('Location: /revisa-tu-correo.html?correo=' . urlencode($email));
    exit;
  }
}
header('Location: /login.html?error=1');
exit;
