<?php
// olvide-clave.php — pide el correo y envía un enlace (válido 1 hora) para crear una contraseña nueva.
// También reenvía el correo de confirmación si la cuenta aún no está confirmada.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/sincronizacion.php';

$enviado = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  $email = trim($_POST['correo'] ?? '');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Escribe un correo válido.';
  } else {
    $pdo = conexionDB();
    $s = $pdo->prepare("SELECT id, nombre, email_verificado, token_reset_expira FROM usuarios WHERE email = ?");
    $s->execute([$email]);
    $u = $s->fetch(PDO::FETCH_ASSOC);
    // Siempre se muestra el mismo mensaje, exista o no la cuenta (no revela quién está registrado).
    if ($u) {
      if ((int)$u['email_verificado'] !== 1) {
        $tv = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE usuarios SET token_verificacion = ? WHERE id = ?")->execute([$tv, $u['id']]);
        enviarCorreoConfirmacion($u['nombre'], $email, SITIO_URL . '/confirmar-correo.php?token=' . $tv);
      } elseif (empty($u['token_reset_expira']) || strtotime($u['token_reset_expira']) < time() + 3300) { // máx. 1 envío cada ~5 min
        $tr = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE usuarios SET token_reset = ?, token_reset_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?")->execute([$tr, $u['id']]);
        $link = SITIO_URL . '/restablecer-clave.php?t=' . $tr;
        enviarCorreo($email, $u['nombre'], 'Restablece tu contraseña',
          '<h2 style="margin-top:0;">Hola, ' . textoSeguro(primerNombre($u['nombre'])) . '</h2>'
          . '<p>Recibimos una solicitud para cambiar tu contraseña. El enlace vale 1 hora.</p>'
          . botonCorreo($link, 'Crear contraseña nueva')
          . '<p style="font-size:13px;">Si no fuiste tú, ignora este correo: tu contraseña no cambia.</p>');
      }
    }
    $enviado = true;
  }
}

paginaInicio('Olvidé mi contraseña');
echo '<div class="card"><h1>Olvidé mi contraseña</h1>';
if ($enviado) {
  echo aviso('ok', 'Si ese correo tiene una cuenta, te enviamos un mensaje (revisa también spam). Si la cuenta no estaba confirmada, te reenviamos el correo de confirmación.');
  echo '<p><a href="/login.html">Volver a iniciar sesión</a></p></div>';
  paginaFin(); exit;
}
if ($error) echo aviso('err', h($error));
?>
  <p>Escribe tu correo y te enviamos un enlace para crear una contraseña nueva.</p>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
    <label class="etq" for="correo">Correo electrónico</label>
    <input class="campo" id="correo" name="correo" type="email" required maxlength="190">
    <p><button class="btn btn-primary" type="submit">Enviar enlace</button></p>
  </form>
  <p class="mut"><a href="/login.html">Volver a iniciar sesión</a></p>
</div>
<?php paginaFin();
