<?php
// terapeuta-registro.php — registro automático de terapeutas (y paso de una cuenta existente a terapeuta).
// Protegido con un CÓDIGO DE TERAPEUTA que solo tú conoces (CODIGO_TERAPEUTA en config.php).
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';

$codigoReal = defined('CODIGO_TERAPEUTA') ? (string)CODIGO_TERAPEUTA : '';
$error = '';
$v = ['nombre' => '', 'correo' => '', 'celular' => ''];

// PUERTA: sin el código correcto no se muestra ni el formulario (la puerta dura 30 minutos).
$abierta = !empty($_SESSION['t_ok']) && (time() - $_SESSION['t_ok']) < 1800;
if ($codigoReal !== '' && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'puerta') {
  verificaCsrf();
  if (($_SESSION['t_intentos'] ?? 0) >= 6) {
    if (empty($_SESSION['t_bloqueo'])) $_SESSION['t_bloqueo'] = time();
    if (time() - $_SESSION['t_bloqueo'] > 600) { $_SESSION['t_intentos'] = 0; unset($_SESSION['t_bloqueo']); }
    else $error = 'Demasiados intentos. Espera unos minutos y vuelve a intentarlo.';
  }
  if (!$error) {
    if (hash_equals($codigoReal, trim($_POST['codigo'] ?? ''))) { session_regenerate_id(true); $_SESSION['t_ok'] = time(); $_SESSION['t_intentos'] = 0; $abierta = true; }
    else { $_SESSION['t_intentos'] = ($_SESSION['t_intentos'] ?? 0) + 1; sleep(1); $error = 'El código no es correcto.'; }
  }
}

if ($codigoReal !== '' && $abierta && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'registro') {
  verificaCsrf();
  if (!empty($_POST['sitio_web'] ?? '')) exit; // campo trampa anti-spam
  $v['nombre']  = trim($_POST['nombre'] ?? '');
  $v['correo']  = trim($_POST['correo'] ?? '');
  $v['celular'] = trim($_POST['celular'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $pass2 = $_POST['clave_confirmar'] ?? '';

  if (!filter_var($v['correo'], FILTER_VALIDATE_EMAIL)) {
    $error = 'Escribe un correo válido.';
  } else {
    $pdo = conexionDB();
    $q = $pdo->prepare("SELECT id, nombre, password_hash, email_verificado, rol FROM usuarios WHERE email = ?");
    $q->execute([$v['correo']]);
    $u = $q->fetch(PDO::FETCH_ASSOC);

    if ($u) {
      // Cuenta existente: se comprueba su contraseña y se le da el rol de terapeuta.
      if (!password_verify($pass, $u['password_hash'] ?? '')) {
        $_SESSION['t_intentos']++;
        $error = 'Ya existe una cuenta con ese correo, pero la contraseña no coincide. Escribe la contraseña de esa cuenta.';
      } elseif (!(int)$u['email_verificado']) {
        $error = 'Esa cuenta aún no confirmó su correo. Abre el correo de confirmación y vuelve aquí.';
      } else {
        $pdo->prepare("UPDATE usuarios SET rol = 'terapeuta' WHERE id = ?")->execute([$u['id']]);
        $_SESSION['t_intentos'] = 0;
        header('Location: /login.html?next=' . urlencode('/portal-terapeuta.php') . '&terapeuta=1');
        exit;
      }
    } else {
      // Cuenta nueva de terapeuta.
      $digitos = preg_replace('/\D+/', '', $v['celular']);
      if (mb_strlen($v['nombre']) < 5 || mb_strpos($v['nombre'], ' ') === false) $error = 'Escribe tu nombre completo.';
      elseif (strlen($digitos) < 10 || strlen($digitos) > 15) $error = 'Escribe un celular válido (con indicativo si es de otro país).';
      elseif (strlen($pass) < 8) $error = 'La contraseña debe tener al menos 8 caracteres.';
      elseif ($pass !== $pass2) $error = 'Las dos contraseñas no coinciden.';
      else {
        $token = bin2hex(random_bytes(32));
        try {
          $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, password_hash, estado_membresia, email_verificado, token_verificacion, rol)
                         VALUES (?, ?, ?, ?, 'activa', 0, ?, 'terapeuta')")
              ->execute([$v['nombre'], $v['correo'], $v['celular'], password_hash($pass, PASSWORD_DEFAULT), $token]);
          $_SESSION['t_intentos'] = 0;
          enviarCorreoConfirmacion($v['nombre'], $v['correo'], SITIO_URL . '/confirmar-correo.php?token=' . $token);
          header('Location: /revisa-tu-correo.html?correo=' . urlencode($v['correo']));
          exit;
        } catch (Throwable $e) {
          error_log('terapeuta-registro: ' . $e->getMessage());
          $error = 'No pudimos crear la cuenta. Intenta de nuevo en un momento.';
        }
      }
    }
  }
}

paginaInicio('Registro de terapeuta');
?>
<div class="hero">
  <h1>Portal del terapeuta</h1>
  <p>Crea tu cuenta de terapeuta o activa el portal en una cuenta que ya tienes.</p>
</div>

<?php if ($codigoReal === ''): ?>
  <div class="err"><strong>Falta un paso de seguridad.</strong><br>
  Para abrir este registro, agrega esta línea en el archivo <code>config.php</code> del servidor (el que está arriba de <code>public_html</code>) con tu propio código secreto:<br>
  <code>define('CODIGO_TERAPEUTA', 'escribe-aqui-tu-codigo');</code></div>
<?php elseif (!$abierta): ?>
<div class="card">
  <h2 style="margin-top:0">Acceso privado</h2>
  <p>Escribe el código de terapeuta para continuar.</p>
  <?php if ($error) echo '<div class="err" role="alert">' . h($error) . '</div>'; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
    <input type="hidden" name="accion" value="puerta">
    <label class="etq" for="t-cod" style="margin-top:0">Código de terapeuta</label>
    <input class="campo" id="t-cod" type="password" name="codigo" required autocomplete="off" autofocus>
    <button type="submit" style="margin-top:18px;background:#F5C343;color:#211A33;border:0;border-radius:30px;padding:14px 28px;font-weight:600;font-size:16px;cursor:pointer">Continuar</button>
  </form>
  <p class="mut" style="margin-top:18px">¿Ya eres terapeuta? <a href="/login.html?next=<?= urlencode('/portal-terapeuta.php') ?>">Ingresa aquí</a></p>
</div>
<script src="/assets/js/password.js"></script>
<?php else: ?>
<div class="card">
  <?php if ($error) echo '<div class="err" role="alert">' . h($error) . '</div>'; ?>
  <form method="post" autocomplete="on">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
    <input type="hidden" name="accion" value="registro">
    <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>

    <label class="etq" for="t-correo" style="margin-top:0">Correo*</label>
    <input class="campo" id="t-correo" type="email" name="correo" required value="<?= h($v['correo']) ?>" autocomplete="email">

    <p class="mut" style="margin:6px 0 0">¿Ya tienes una cuenta? Usa su correo y su contraseña: el nombre y el celular no hacen falta.</p>

    <label class="etq" for="t-nombre">Nombre completo (solo si es cuenta nueva)</label>
    <input class="campo" id="t-nombre" type="text" name="nombre" value="<?= h($v['nombre']) ?>" autocomplete="name">

    <label class="etq" for="t-cel">Celular (solo si es cuenta nueva)</label>
    <input class="campo" id="t-cel" type="tel" name="celular" value="<?= h($v['celular']) ?>" autocomplete="tel">

    <label class="etq" for="t-pass">Contraseña*</label>
    <input class="campo" id="t-pass" type="password" name="password" required minlength="8" autocomplete="new-password">

    <label class="etq" for="t-pass2">Confirma la contraseña (solo si es cuenta nueva)</label>
    <input class="campo" id="t-pass2" type="password" name="clave_confirmar" minlength="8" autocomplete="new-password">

    <button type="submit" class="btn" style="margin-top:22px;background:#F5C343;color:#211A33;border:0;border-radius:30px;padding:15px 30px;font-weight:600;font-size:16px;cursor:pointer">Entrar al portal del terapeuta</button>
  </form>
  <p class="mut" style="margin-top:18px">¿Ya eres terapeuta? <a href="/login.html?next=<?= urlencode('/portal-terapeuta.php') ?>">Ingresa aquí</a></p>
</div>
<script src="/assets/js/password.js"></script>
<?php endif; ?>
<?php paginaFin();
