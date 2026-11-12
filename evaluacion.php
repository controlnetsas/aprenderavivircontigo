<?php
// evaluacion.php — aterrizaje del enlace general que envía el terapeuta: registro obligatorio.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/sincronizacion.php';

$pdo = conexionDB();
$token = $_GET['t'] ?? ($_POST['t'] ?? '');
$error = '';

$enlace = null;
if (is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token)) {
  $s = $pdo->prepare("SELECT * FROM enlaces_evaluacion WHERE token = ? AND activo = 1");
  $s->execute([$token]);
  $enlace = $s->fetch(PDO::FETCH_ASSOC) ?: null;
}
if (!$enlace) {
  paginaInicio('Enlace no válido');
  echo '<div class="card"><h1>Este enlace ya no es válido</h1><p>Pídele a tu terapeuta un enlace nuevo.</p>'
     . '<p><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, necesito el enlace de la evaluación inicial.') . '">Pedirlo por WhatsApp</a></p></div>';
  paginaFin();
  exit;
}

// Si ya inició sesión: crear su evaluación o continuar.
$usuario = usuarioActual();
if ($usuario) {
  if (!evaluacionDeUsuario($pdo, $usuario['id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comenzar'])) {
    verificaCsrf();
    $pdo->prepare("INSERT INTO evaluaciones (usuario_id, terapeuta_usuario_id, enlace_id, consentimiento_en) VALUES (?,?,?,NOW())")
        ->execute([$usuario['id'], $enlace['terapeuta_usuario_id'], $enlace['id']]);
  }
  if (evaluacionDeUsuario($pdo, $usuario['id'])) { header('Location: /mi-evaluacion.php'); exit; }
  paginaInicio('Evaluación inicial');
  echo '<div class="card"><h1>Hola, ' . h(primerNombre($usuario['nombre'])) . '</h1>'
     . '<p>Tu terapeuta te invita a una evaluación inicial: una ficha con tus datos básicos y tres tests de autoindagación. Al terminar, se genera un informe para tu terapeuta.</p>'
     . '<form method="post"><input type="hidden" name="csrf" value="' . h(tokenCsrf()) . '"><input type="hidden" name="t" value="' . h($token) . '">'
     . '<input type="hidden" name="comenzar" value="1"><label style="display:flex;gap:10px;margin:14px 0"><input type="checkbox" required style="margin-top:4px"><span class="mut">' . h(TEXTO_CONSENTIMIENTO_EVAL) . '</span></label>'
     . '<button class="btn btn-primary" type="submit">Comenzar mi evaluación</button></form></div>';
  paginaFin();
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['sitio_web'] ?? '')) exit; // campo trampa
  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['correo'] ?? '');
  $celular = trim($_POST['celular'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';
  $digitos = preg_replace('/\D+/', '', $celular);

  if (mb_strlen($nombre) < 5 || mb_strpos(trim($nombre), ' ') === false || mb_strlen($nombre) > 150) {
    $error = 'Escribe tu nombre completo (nombre y apellido).';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Escribe un correo electrónico válido.';
  } elseif (strlen($digitos) < 10 || strlen($digitos) > 15) {
    $error = 'Escribe tu número de celular (con indicativo si es de otro país).';
  } elseif (strlen($password) < 8) {
    $error = 'La contraseña debe tener al menos 8 caracteres.';
  } elseif ($password !== $password2) {
    $error = 'Las contraseñas no coinciden.';
  } elseif (empty($_POST['consentimiento'])) {
    $error = 'Para continuar necesitas aceptar el uso de tus datos.';
  } else {
    $ex = $pdo->prepare("SELECT id, email_verificado FROM usuarios WHERE email = ?");
    $ex->execute([$email]);
    $existente = $ex->fetch(PDO::FETCH_ASSOC);
    if ($existente && (int)$existente['email_verificado'] === 1) {
      $error = 'Ese correo ya tiene cuenta. <a href="/login.html">Inicia sesión</a> y vuelve a abrir este enlace.';
    } elseif ($existente) {
      $error = 'Ese correo ya se registró pero falta confirmarlo. <a href="/olvide-clave.php">Reenviar el correo de confirmación</a>.';
    } else {
      try {
        $pdo->beginTransaction();
        $tokenVer = bin2hex(random_bytes(32));
        $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, password_hash, estado_membresia, email_verificado, token_verificacion) VALUES (?,?,?,?, 'pendiente', 0, ?)")
            ->execute([$nombre, $email, $celular, password_hash($password, PASSWORD_DEFAULT), $tokenVer]);
        $uid = (int)$pdo->lastInsertId();
        $pdo->prepare("INSERT INTO evaluaciones (usuario_id, terapeuta_usuario_id, enlace_id, consentimiento_en) VALUES (?,?,?,NOW())")
            ->execute([$uid, $enlace['terapeuta_usuario_id'], $enlace['id']]);
        $pdo->commit();
      } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('evaluacion.php: ' . $e->getMessage());
        $error = 'No pudimos crear tu cuenta. Intenta de nuevo o escríbenos por WhatsApp.';
        $uid = 0;
      }
      if (!$error && $uid) {
        try {
          sincronizarContactoHubSpot($nombre, $email);
          sincronizarContactoBrevo($nombre, $email, $celular, 'Evaluación inicial');
        } catch (Throwable $e) { error_log('sync: ' . $e->getMessage()); }
        enviarCorreoConfirmacion($nombre, $email, SITIO_URL . '/confirmar-correo.php?token=' . $tokenVer);
        header('Location: /revisa-tu-correo.html?correo=' . urlencode($email));
        exit;
      }
    }
  }
}

paginaInicio('Evaluación inicial');
?>
<div class="card">
  <h1>Evaluación inicial</h1>
  <p>Tu terapeuta te invita a crear tu cuenta. Después llenarás una ficha con tus datos básicos y harás tres tests de autoindagación (genealogía, heridas de la infancia y Reconóce TE). Con eso se genera un informe que ayuda a tu terapeuta a acompañarte.</p>
  <?php if ($error) echo aviso('err', $error); ?>
  <form method="post" autocomplete="off">
    <input type="hidden" name="t" value="<?= h($token) ?>">
    <div style="position:absolute;left:-9999px" aria-hidden="true"><input name="sitio_web" tabindex="-1" autocomplete="off"></div>
    <label class="etq" for="nombre">Nombre completo</label>
    <input class="campo" id="nombre" name="nombre" required maxlength="150" value="<?= h($_POST['nombre'] ?? '') ?>">
    <label class="etq" for="correo">Correo electrónico</label>
    <input class="campo" id="correo" name="correo" type="email" required maxlength="190" value="<?= h($_POST['correo'] ?? '') ?>">
    <p class="mut" style="margin:4px 0 0">Te enviaremos un correo para confirmar que es tuyo.</p>
    <label class="etq" for="celular">Número de celular</label>
    <input class="campo" id="celular" name="celular" type="tel" required maxlength="30" value="<?= h($_POST['celular'] ?? '') ?>" placeholder="Ej.: 3001234567">
    <label class="etq" for="password">Contraseña (mínimo 8 caracteres)</label>
    <input class="campo" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
    <label class="etq" for="password2">Confirma tu contraseña</label>
    <input class="campo" id="password2" name="password2" type="password" minlength="8" required autocomplete="new-password">
    <label style="display:flex;gap:10px;margin:18px 0;align-items:flex-start">
      <input type="checkbox" name="consentimiento" value="1" required style="margin-top:4px">
      <span class="mut"><?= h(TEXTO_CONSENTIMIENTO_EVAL) ?></span>
    </label>
    <button class="btn btn-primary btn-block" type="submit">Crear mi cuenta</button>
  </form>
  <p class="mut" style="margin-top:14px">¿Ya tienes cuenta? <a href="/login.html">Inicia sesión</a> y vuelve a abrir este enlace.</p>
</div>
<p class="mut" style="margin-top:14px">Esta evaluación es educativa y complementaria; no reemplaza atención médica ni psicológica.</p>
<script src="/assets/js/password.js"></script>
<?php paginaFin();
