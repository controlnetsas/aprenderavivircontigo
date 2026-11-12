<?php
// evaluaciones.php — panel del terapeuta: enlace general para enviar y lista de evaluaciones recibidas.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
$terapeuta = requiereTerapeuta();
$pdo = conexionDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  if (($_POST['accion'] ?? '') === 'nuevo_enlace') {
    $pdo->prepare("UPDATE enlaces_evaluacion SET activo = 0 WHERE terapeuta_usuario_id = ?")->execute([$terapeuta['id']]);
    $pdo->prepare("INSERT INTO enlaces_evaluacion (terapeuta_usuario_id, token) VALUES (?, ?)")->execute([$terapeuta['id'], bin2hex(random_bytes(32))]);
  }
  if (($_POST['accion'] ?? '') === 'invitar') {
    $nom = trim($_POST['nombre'] ?? ''); $mail = trim($_POST['correo'] ?? ''); $cel = trim($_POST['celular'] ?? '');
    $tk = $pdo->prepare("SELECT token FROM enlaces_evaluacion WHERE terapeuta_usuario_id = ? AND activo = 1 ORDER BY id DESC LIMIT 1");
    $tk->execute([$terapeuta['id']]); $tok = $tk->fetchColumn();
    if ($nom !== '' && filter_var($mail, FILTER_VALIDATE_EMAIL) && $tok) {
      $lnk = SITIO_URL . '/bienvenida.php?t=' . $tok;
      $primer = explode(' ', $nom)[0];
      $okc = enviarCorreo($mail, $nom, 'Tu evaluación inicial — Aprender a Vivir Contigo',
        '<h2 style="margin-top:0;">Hola, ' . textoSeguro($primer) . '</h2>'
        . '<p>Gracias por pedir tu terapia: dar este paso es un acto de valentía. Entra con el botón: ahí te doy la bienvenida y te cuento cómo será el proceso. Para prepararlo, necesito que completes tu evaluación inicial. Es sencillo:</p>'
        . '<ol><li>Entra con el botón y <strong>crea tu cuenta</strong> (nombre completo, correo, celular y contraseña).</li>'
        . '<li><strong>Confirma tu correo</strong> con el mensaje que te llegará.</li>'
        . '<li><strong>Llena tu ficha</strong> con tus datos básicos.</li>'
        . '<li><strong>Haz los tres tests</strong>: genealogía, eneatipos y heridas de la infancia.</li></ol>'
        . '<p>Puedes hacerlo a tu ritmo. Lo que respondas queda guardado y solo lo veo yo.</p>'
        . botonCorreo($lnk, 'Leer mi bienvenida y empezar'));
      $_SESSION['flash_inv'] = $okc ? 'Invitación enviada a ' . $mail . '.' : 'No se pudo enviar el correo. Envía el enlace por WhatsApp.';
      if ($cel !== '') $_SESSION['flash_wa'] = linkWhatsApp($cel, "Hola {$primer}, soy Carlos de Aprender a Vivir Contigo. Te envié a tu correo ({$mail}) las indicaciones para tu evaluación inicial. Si no te llega, revisa spam o entra aquí:\n" . $lnk);
    } else { $_SESSION['flash_inv'] = 'Escribe nombre y un correo válido.'; }
  }
  header('Location: /evaluaciones.php'); exit;
}
$flashInv = $_SESSION['flash_inv'] ?? ''; $flashWa = $_SESSION['flash_wa'] ?? '';
unset($_SESSION['flash_inv'], $_SESSION['flash_wa']);

$e = $pdo->prepare("SELECT token FROM enlaces_evaluacion WHERE terapeuta_usuario_id = ? AND activo = 1 ORDER BY id DESC LIMIT 1");
$e->execute([$terapeuta['id']]);
$token = $e->fetchColumn();
if (!$token) {
  $token = bin2hex(random_bytes(32));
  $pdo->prepare("INSERT INTO enlaces_evaluacion (terapeuta_usuario_id, token) VALUES (?, ?)")->execute([$terapeuta['id'], $token]);
}
$link = SITIO_URL . '/bienvenida.php?t=' . $token; // página de bienvenida; su botón lleva al registro
$textoWa = "Hola, soy Carlos de Aprender a Vivir Contigo. Para tu proceso, entra a este enlace, crea tu cuenta y llena tu evaluación inicial (una ficha y tres tests):\n" . $link;

$l = $pdo->prepare("SELECT e.id, e.estado, e.ficha_completa, e.creado_en, e.completada_en, u.nombre, u.email, u.telefono, u.email_verificado, u.id AS uid
                    FROM evaluaciones e JOIN usuarios u ON u.id = e.usuario_id WHERE e.terapeuta_usuario_id = ? ORDER BY e.id DESC LIMIT 200");
$l->execute([$terapeuta['id']]);
$lista = $l->fetchAll(PDO::FETCH_ASSOC);

paginaInicio('Evaluaciones iniciales');
?>
<p class="mut"><a href="/terapeuta.php">← Panel del terapeuta</a></p>
<h1>Evaluaciones iniciales</h1>
<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Tu enlace para enviar</h2>
  <p>Mándaselo a quien quieras evaluar: primero verá la página de bienvenida y desde ahí empieza su registro. Quien lo abre debe crear su cuenta (nombre completo, correo, celular y contraseña), confirmar su correo, y luego llena su ficha y hace los tests.</p>
  <input class="campo" id="enlace" readonly value="<?= h($link) ?>" onclick="this.select()">
  <p class="fila" style="margin-top:10px">
    <button class="btn btn-outline-dark" type="button" onclick="navigator.clipboard.writeText(document.getElementById('enlace').value);this.textContent='¡Copiado!'">Copiar enlace</button>
    <a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/?text=<?= rawurlencode($textoWa) ?>">Enviar por WhatsApp</a>
  </p>
  <form method="post" onsubmit="return confirm('El enlace anterior dejará de funcionar. ¿Generar uno nuevo?')">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="nuevo_enlace">
    <button class="btn btn-outline-dark" type="submit">Generar un enlace nuevo (desactiva el anterior)</button>
  </form>
</div>
<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Invitar a un consultante por correo</h2>
  <p class="mut">Le llega un correo con las indicaciones y el botón para empezar. Si pones su celular, también te dejo listo el mensaje de WhatsApp.</p>
  <?php if ($flashInv) echo aviso(strpos($flashInv, 'enviada') !== false ? 'ok' : 'err', h($flashInv)); ?>
  <?php if ($flashWa): ?><p><a class="btn btn-primary" target="_blank" rel="noopener" href="<?= h($flashWa) ?>">Avisarle también por WhatsApp</a></p><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="invitar">
    <div class="fila">
      <div style="flex:1;min-width:180px"><label class="etq" for="inv_nombre">Nombre</label><input class="campo" id="inv_nombre" name="nombre" required maxlength="150"></div>
      <div style="flex:1;min-width:200px"><label class="etq" for="inv_correo">Correo</label><input class="campo" id="inv_correo" name="correo" type="email" required maxlength="190"></div>
      <div style="flex:1;min-width:160px"><label class="etq" for="inv_cel">Celular (opcional)</label><input class="campo" id="inv_cel" name="celular" type="tel" maxlength="30"></div>
    </div>
    <p><button class="btn btn-primary" type="submit">Enviar invitación por correo</button></p>
  </form>
</div>
<div class="card">
  <h2 style="margin-top:0">Evaluaciones recibidas</h2>
  <?php if (!$lista): ?><p class="mut">Aún no hay evaluaciones.</p><?php else: ?>
  <table class="tabla">
    <tr><th>Persona</th><th>Estado</th><th>Fecha</th><th></th></tr>
    <?php foreach ($lista as $r):
      $estado = $r['estado'] === 'completada' ? 'Completada' : ((int)$r['email_verificado'] !== 1 ? 'Correo sin confirmar' : ($r['estado'] === 'en_curso' ? 'En curso' : 'Registrada')); ?>
    <tr>
      <td><strong><?= h($r['nombre']) ?></strong><br><span class="mut"><?= h($r['email']) ?> · <?= h($r['telefono']) ?></span></td>
      <td><span class="chip"><?= h($estado) ?></span></td>
      <td class="mut"><?= h(substr((string)($r['completada_en'] ?: $r['creado_en']), 0, 10)) ?></td>
      <td><a class="btn btn-primary" href="/consultante.php?id=<?= (int)$r['id'] ?>">Ver consultante</a>
        <?php if ($r['estado'] === 'completada'): ?> <a class="btn btn-outline-dark" href="/informe.php?id=<?= (int)$r['id'] ?>">Informe</a><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
<?php paginaFin();
