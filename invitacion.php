<?php
// invitacion.php — aterrizaje del enlace que envía el terapeuta. Obliga a registrarse con nombre y contraseña.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/sincronizacion.php';

$pdo = conexionDB();
$token = $_GET['t'] ?? ($_POST['t'] ?? '');
$error = '';

function cargarConsulta($pdo, $token) {
  if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return null;
  $s = $pdo->prepare("SELECT * FROM consultas WHERE token = ? AND fecha_inicio IS NULL AND token_expira > NOW()");
  $s->execute([$token]);
  return $s->fetch(PDO::FETCH_ASSOC) ?: null;
}

$consulta = cargarConsulta($pdo, $token);

if (!$consulta) {
  paginaInicio('Enlace no válido');
  echo '<div class="card"><h1>Este enlace ya no es válido</h1>'
     . '<p>Puede haber vencido (dura 14 días) o ya fue usado. Si ya creaste tu cuenta, <a href="/login.html">inicia sesión</a> y entra a "Mi camino".</p>'
     . '<p><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, mi enlace de acompañamiento venció. ¿Me envías uno nuevo?') . '">Pedir un enlace nuevo por WhatsApp</a></p></div>';
  paginaFin();
  exit;
}

$existe = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$existe->execute([$consulta['email']]);
$cuentaExistente = $existe->fetchColumn();

if ($cuentaExistente) {
  // Ya tiene cuenta: debe iniciar sesión; Mi camino vincula la consulta por correo.
  paginaInicio('Ya tienes cuenta');
  echo '<div class="card"><h1>Ya tienes una cuenta</h1>'
     . '<p>Hola, ' . h(primerNombre($consulta['nombre'])) . '. El correo ' . h(ocultarCorreo($consulta['email'])) . ' ya está registrado. '
     . 'Inicia sesión con tu contraseña y tu camino aparecerá en "Mi camino".</p>'
     . '<p><a class="btn btn-primary" href="/login.html">Iniciar sesión</a></p></div>';
  paginaFin();
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $password = $_POST['password'] ?? '';
  $acepta = !empty($_POST['consentimiento']);

  if (!empty($_POST['sitio_web'] ?? '')) exit; // campo trampa
  if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 150) {
    $error = 'Escribe tu nombre completo.';
  } elseif (strlen($password) < 8) {
    $error = 'La contraseña debe tener al menos 8 caracteres.';
  } elseif (!$acepta) {
    $error = 'Para activar tu camino necesitas aceptar el uso de tus datos.';
  } else {
    try {
      $pdo->beginTransaction();
      $reclamo = $pdo->prepare(
        "UPDATE consultas SET fecha_inicio = CURDATE(), estado = 'en_proceso', consentimiento_en = NOW(), nombre = ?, token_expira = NOW()
         WHERE id = ? AND fecha_inicio IS NULL"
      );
      $reclamo->execute([$nombre, $consulta['id']]);
      if ($reclamo->rowCount() !== 1) throw new RuntimeException('enlace usado');

      $ins = $pdo->prepare(
        "INSERT INTO usuarios (nombre, email, telefono, password_hash, estado_membresia, email_verificado)
         VALUES (?, ?, ?, ?, 'pendiente', 1)"
      );
      $ins->execute([$nombre, $consulta['email'], $consulta['whatsapp'], password_hash($password, PASSWORD_DEFAULT)]);
      $usuarioId = (int)$pdo->lastInsertId();

      $pdo->prepare("UPDATE consultas SET usuario_id = ? WHERE id = ?")->execute([$usuarioId, $consulta['id']]);

      $rec = $pdo->prepare("INSERT IGNORE INTO consultante_talleres (usuario_id, taller, estado) VALUES (?, ?, 'recomendado')");
      foreach (talleresDeConsulta($consulta['talleres_recomendados']) as $taller) {
        $rec->execute([$usuarioId, $taller]);
      }
      $pdo->commit();
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      error_log('invitacion.php: ' . $e->getMessage());
      $error = 'No pudimos activar tu camino (el enlace pudo haberse usado ya). Intenta de nuevo o escríbenos por WhatsApp.';
      $usuarioId = 0;
    }

    if (!$error && $usuarioId) {
      try {
        sincronizarContactoHubSpot($nombre, $consulta['email']);
        sincronizarContactoBrevo($nombre, $consulta['email'], (string)$consulta['whatsapp'], 'Plan de acompañamiento');
      } catch (Throwable $e) { error_log('sync: ' . $e->getMessage()); }

      session_regenerate_id(true);
      $_SESSION['usuario_id'] = $usuarioId;
      header('Location: /mi-camino.php?bienvenida=1');
      exit;
    }
  }
}

paginaInicio('Activa tu camino');
?>
<div class="card">
  <h1>Activa tu camino, <?= h(primerNombre($consulta['nombre'])) ?></h1>
  <p>Después de tu consulta preparé un plan con ejercicios diarios y los talleres que pueden acompañarte. Crea tu cuenta para verlo y llevar tu registro.</p>
  <?php if ($error) echo aviso('err', h($error)); ?>
  <form method="post" autocomplete="off">
    <input type="hidden" name="t" value="<?= h($token) ?>">
    <div style="position:absolute;left:-9999px" aria-hidden="true"><input name="sitio_web" tabindex="-1" autocomplete="off"></div>
    <label class="etq" for="nombre">Tu nombre completo</label>
    <input class="campo" id="nombre" name="nombre" required maxlength="150" value="<?= h($_POST['nombre'] ?? $consulta['nombre']) ?>">
    <label class="etq">Tu correo</label>
    <input class="campo" value="<?= h(ocultarCorreo($consulta['email'])) ?>" disabled>
    <label class="etq" for="password">Crea una contraseña (mínimo 8 caracteres)</label>
    <input class="campo" id="password" name="password" type="password" minlength="8" required autocomplete="new-password">
    <label style="display:flex;gap:10px;margin:18px 0;align-items:flex-start">
      <input type="checkbox" name="consentimiento" value="1" required style="margin-top:4px">
      <span class="mut"><?= h(TEXTO_CONSENTIMIENTO) ?></span>
    </label>
    <button class="btn btn-primary btn-block" type="submit">Activar mi camino</button>
  </form>
</div>
<p class="mut" style="margin-top:14px">Este acompañamiento es educativo y complementario; no reemplaza atención médica ni psicológica.</p>
<?php paginaFin();
