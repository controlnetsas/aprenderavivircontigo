<?php
// cambiar-clave.php — el usuario con sesión cambia su contraseña.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
requiereLogin();
$pdo = conexionDB();
$usuario = usuarioActual();
if (!$usuario) { header('Location: /login.html'); exit; }

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  $actual = $_POST['actual'] ?? '';
  $nueva = $_POST['password'] ?? '';
  $nueva2 = $_POST['password2'] ?? '';

  $s = $pdo->prepare("SELECT password_hash FROM usuarios WHERE id = ?");
  $s->execute([$usuario['id']]);
  $hash = (string)$s->fetchColumn();

  if (!$hash || !password_verify($actual, $hash)) {
    $mensaje = aviso('err', 'La contraseña actual no es correcta.');
  } elseif (strlen($nueva) < 8) {
    $mensaje = aviso('err', 'La nueva contraseña debe tener al menos 8 caracteres.');
  } elseif ($nueva !== $nueva2) {
    $mensaje = aviso('err', 'Las contraseñas nuevas no coinciden.');
  } elseif ($nueva === $actual) {
    $mensaje = aviso('err', 'La nueva contraseña debe ser distinta de la actual.');
  } else {
    $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?")
        ->execute([password_hash($nueva, PASSWORD_DEFAULT), $usuario['id']]);
    session_regenerate_id(true);
    $mensaje = aviso('ok', 'Listo, tu contraseña quedó actualizada.');
  }
}

paginaInicio('Cambiar contraseña');
?>
<p class="mut"><a href="/membresia.php">← Volver</a></p>
<div class="card" style="max-width:460px">
  <h1>Cambiar mi contraseña</h1>
  <?= $mensaje ?>
  <form method="post" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
    <label class="etq" for="actual">Contraseña actual</label>
    <input class="campo" id="actual" name="actual" type="password" required autocomplete="current-password">
    <label class="etq" for="password">Nueva contraseña (mínimo 8 caracteres)</label>
    <input class="campo" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
    <label class="etq" for="password2">Confirma la nueva contraseña</label>
    <input class="campo" id="password2" name="password2" type="password" minlength="8" required autocomplete="new-password">
    <p style="margin-top:18px"><button class="btn btn-primary btn-block" type="submit">Guardar contraseña</button></p>
  </form>
</div>
<script src="/assets/js/password.js"></script>
<?php paginaFin();
