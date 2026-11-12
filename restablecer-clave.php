<?php
// restablecer-clave.php — crea una contraseña nueva con el enlace recibido por correo.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';

$pdo = conexionDB();
$token = $_GET['t'] ?? ($_POST['t'] ?? '');
$u = null;
if (is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token)) {
  $s = $pdo->prepare("SELECT id FROM usuarios WHERE token_reset = ? AND token_reset_expira > NOW()");
  $s->execute([$token]);
  $u = $s->fetch(PDO::FETCH_ASSOC) ?: null;
}
paginaInicio('Nueva contraseña');
if (!$u) {
  echo '<div class="card"><h1>Enlace vencido o no válido</h1><p>Pide uno nuevo.</p><p><a class="btn btn-primary" href="/olvide-clave.php">Pedir otro enlace</a></p></div>';
  paginaFin(); exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  $p1 = $_POST['password'] ?? ''; $p2 = $_POST['password2'] ?? '';
  if (strlen($p1) < 8) $error = 'La contraseña debe tener al menos 8 caracteres.';
  elseif ($p1 !== $p2) $error = 'Las contraseñas no coinciden.';
  else {
    $pdo->prepare("UPDATE usuarios SET password_hash = ?, token_reset = NULL, token_reset_expira = NULL, email_verificado = 1 WHERE id = ?")
        ->execute([password_hash($p1, PASSWORD_DEFAULT), $u['id']]);
    echo '<div class="card"><h1>Listo</h1>' . aviso('ok', 'Tu contraseña fue cambiada.') . '<p><a class="btn btn-primary" href="/login.html">Iniciar sesión</a></p></div>';
    paginaFin(); exit;
  }
}
?>
<div class="card">
  <h1>Crea tu contraseña nueva</h1>
  <?php if ($error) echo aviso('err', h($error)); ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="t" value="<?= h($token) ?>">
    <label class="etq" for="password">Contraseña nueva (mínimo 8 caracteres)</label>
    <input class="campo" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
    <label class="etq" for="password2">Confirma la contraseña</label>
    <input class="campo" id="password2" name="password2" type="password" minlength="8" required autocomplete="new-password">
    <p><button class="btn btn-primary" type="submit">Guardar contraseña</button></p>
  </form>
</div>
<script src="/assets/js/password.js"></script>
<?php paginaFin();
