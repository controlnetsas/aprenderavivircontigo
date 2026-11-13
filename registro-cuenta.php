<?php
// registro-cuenta.php — registro único: sirve al registro general y al del enlace de evaluación del terapeuta.
// Si llega el código `t` de un enlace activo, además de la cuenta crea la evaluación inicial de la persona.
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sincronizacion.php';
require_once __DIR__ . '/enlaces-evaluacion.php';

$origen = ($_POST['origen'] ?? '') === 'bienvenida';
$tokenEnlace = $_POST['t'] ?? '';
$tokenEnlace = (is_string($tokenEnlace) && preg_match('/^[a-f0-9]{64}$/', $tokenEnlace)) ? $tokenEnlace : '';
$volver = $origen
  ? '/bienvenida.php?error=%s' . ($tokenEnlace ? '&t=' . $tokenEnlace : '') . '#registro'
  : '/registrate.html?error=%s';
function irError($plantilla, $codigo) { header('Location: ' . sprintf($plantilla, $codigo)); exit; }
// Si algo falla de forma inesperada, nunca se deja la pantalla en blanco: se vuelve al formulario con un aviso.
set_exception_handler(function ($e) use ($volver) { error_log('registro-cuenta.php: ' . $e->getMessage()); irError($volver, '1'); });

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['correo'] ?? '');
  $telefono = trim($_POST['whatsapp'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';

  if (!empty($_POST['cp_extra'] ?? '')) irError($volver, '1'); // campo trampa anti-spam

  if ($password !== $password2) irError($volver, 'clave');

  $pdo = conexionDB();

  // Registro desde un enlace del terapeuta: el enlace debe estar activo y los datos deben estar completos.
  $enlace = null;
  if ($tokenEnlace) {
    $enlace = enlaceVigente($pdo, $tokenEnlace);
    if (!$enlace) irError($volver, 'enlace');
    $digitos = preg_replace('/\D+/', '', $telefono);
    if (mb_strlen($nombre) < 5 || mb_strpos($nombre, ' ') === false || strlen($digitos) < 10 || strlen($digitos) > 15 || empty($_POST['consentimiento'])) {
      irError($volver, 'datos');
    }
  }

  if ($nombre && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 8) {
    $existe = $pdo->prepare("SELECT id, email_verificado FROM usuarios WHERE email = ?");
    $existe->execute([$email]);
    if ($existe->fetch(PDO::FETCH_ASSOC)) irError($volver, 'correo-existente');

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));

    try {
      $pdo->beginTransaction();
      $pdo->prepare(
        "INSERT INTO usuarios (nombre, email, telefono, password_hash, estado_membresia, email_verificado, token_verificacion)
         VALUES (?, ?, ?, ?, 'pendiente', 0, ?)"
      )->execute([$nombre, $email, $telefono, $hash, $token]);
      $uid = (int)$pdo->lastInsertId();
      if ($enlace) {
        if (!consumirEnlace($pdo, $enlace['id'])) throw new RuntimeException('enlace agotado');
        crearEvaluacion($pdo, $uid, $enlace);
        otorgarAccesoConsultante($pdo, $uid); // 3 meses de membresía por registrarse con el enlace
      }
      $pdo->commit();
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      error_log('registro-cuenta.php: ' . $e->getMessage());
      irError($volver, $e->getMessage() === 'enlace agotado' ? 'enlace' : '1');
    }

    try {
      sincronizarContactoHubSpot($nombre, $email);
      sincronizarContactoBrevo($nombre, $email, $telefono, $enlace ? 'Evaluación inicial' : 'Boletín general');
    } catch (Throwable $e) { error_log('sync: ' . $e->getMessage()); }

    try { enviarCorreoConfirmacion($nombre, $email, SITIO_URL . '/confirmar-correo.php?token=' . $token); }
    catch (Throwable $e) { error_log('correo confirmación: ' . $e->getMessage()); }

    header('Location: /revisa-tu-correo.html?correo=' . urlencode($email));
    exit;
  }
}
irError($volver, '1');
