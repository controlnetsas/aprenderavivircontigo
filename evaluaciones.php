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
  header('Location: /evaluaciones.php'); exit;
}

$e = $pdo->prepare("SELECT token FROM enlaces_evaluacion WHERE terapeuta_usuario_id = ? AND activo = 1 ORDER BY id DESC LIMIT 1");
$e->execute([$terapeuta['id']]);
$token = $e->fetchColumn();
if (!$token) {
  $token = bin2hex(random_bytes(32));
  $pdo->prepare("INSERT INTO enlaces_evaluacion (terapeuta_usuario_id, token) VALUES (?, ?)")->execute([$terapeuta['id'], $token]);
}
$link = SITIO_URL . '/evaluacion.php?t=' . $token;
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
  <p>Mándaselo a quien quieras evaluar. Quien lo abre debe crear su cuenta (nombre completo, correo, celular y contraseña), confirmar su correo, y luego llena su ficha y hace los tests.</p>
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
      <td><?php if ($r['estado'] === 'completada'): ?><a class="btn btn-primary" href="/informe.php?id=<?= (int)$r['id'] ?>">Ver informe</a><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
<?php paginaFin();
