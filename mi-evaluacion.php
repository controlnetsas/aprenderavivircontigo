<?php
// mi-evaluacion.php — la persona llena su ficha, hace los tests y genera el informe para el terapeuta.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/ficha-campos.php';
require_once __DIR__ . '/informe-evaluacion.php';
requiereLogin();
$pdo = conexionDB();
$usuario = usuarioActual();
if (!$usuario) { header('Location: /login.html'); exit; }
$ev = evaluacionDeUsuario($pdo, $usuario['id']);
if (!$ev) { header('Location: /membresia.php'); exit; }

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  $accion = $_POST['accion'] ?? '';
  if ($accion === 'ficha') {
    $f = leerFichaPost();
    $completa = ($f['edad'] !== null && $f['sintomas'] !== null) ? 1 : 0;
    $pdo->prepare("UPDATE evaluaciones SET edad=?, sexo=?, sintomas=?, situacion_sentimental=?, hijos=?, hijos_no_nacidos=?, estado_salud=?, trabajo_actual=?, profesion=?,
                   conflictos_emocionales=?, conflicto_descripcion=?, heridas_infancia=?, ficha_completa=?, estado = IF(estado='registrada','en_curso',estado) WHERE id = ?")
        ->execute([$f['edad'], $f['sexo'], $f['sintomas'], $f['situacion_sentimental'], $f['hijos'], $f['hijos_no_nacidos'], $f['estado_salud'], $f['trabajo_actual'], $f['profesion'],
                   $f['conflictos_emocionales'], $f['conflicto_descripcion'], $f['heridas_infancia'], $completa, $ev['id']]);
    $mensaje = $completa ? aviso('ok', 'Ficha guardada.') : aviso('err', 'Ficha guardada, pero para marcarla completa necesitamos al menos tu edad y tus síntomas.');
    $ev = evaluacionDeUsuario($pdo, $usuario['id']);
  }
  if ($accion === 'finalizar') {
    $av = avanceTestsEvaluacion($pdo, $usuario['id']);
    if ($ev['ficha_completa'] && $av['genealogia'] && $av['eneatipo'] && $av['herida']) {
      $inf = construirInformeEvaluacion($pdo, $ev, $usuario);
      $pdo->prepare("UPDATE evaluaciones SET informe = ?, estado = 'completada', completada_en = NOW() WHERE id = ?")->execute([$inf['html'], $ev['id']]);
      $t = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = ?");
      $t->execute([$ev['terapeuta_usuario_id']]);
      if ($ter = $t->fetch(PDO::FETCH_ASSOC)) {
        $link = SITIO_URL . '/informe.php?id=' . (int)$ev['id'];
        $lis = '';
        foreach ($inf['destacados'] as $d) $lis .= '<li>' . textoSeguro($d) . '</li>';
        enviarCorreo($ter['email'], $ter['nombre'], 'Nuevo informe de evaluación: ' . $usuario['nombre'],
          '<h2 style="margin-top:0;">Nueva evaluación completada</h2>'
          . '<p><strong>' . textoSeguro($usuario['nombre']) . '</strong> terminó su ficha y sus tests.</p>'
          . '<p>Resumen de los tests:</p><ul>' . $lis . '</ul>'
          . '<p style="font-size:13px;">El informe completo (con ficha y respuestas) está en el portal; para proteger sus datos de salud no se incluye en el correo.</p>'
          . botonCorreo($link, 'Abrir el informe completo'));
      }
      $mensaje = aviso('ok', 'Tu informe quedó guardado y se envió a tu terapeuta.');
      $ev = evaluacionDeUsuario($pdo, $usuario['id']);
    } else {
      $mensaje = aviso('err', 'Aún falta completar la ficha o alguno de los tests.');
    }
  }
}

$av = avanceTestsEvaluacion($pdo, $usuario['id']);
$todo = $ev['ficha_completa'] && $av['genealogia'] && $av['eneatipo'] && $av['herida'];
$ok = fn($b) => $b ? '<span class="chip" style="background:#e4f3e6">✓ Listo</span>' : '<span class="chip">Pendiente</span>';

paginaInicio('Mi evaluación');
echo '<p class="mut"><a href="/membresia.php">Mi membresía</a> · <a href="/cambiar-clave.php">Cambiar contraseña</a></p>';
echo '<h1>Mi evaluación inicial</h1>';
if (!empty($_GET['confirmado'])) echo aviso('ok', 'Correo confirmado. ¡Bienvenido/a!');
echo $mensaje;
echo '<p class="mut">Cuatro pasos: tu ficha y tres tests. Al terminar se genera un informe para tu terapeuta. Puedes ir a tu ritmo y volver cuando quieras.</p>';
?>
<div class="card" style="margin-bottom:14px">
  <h2 style="margin-top:0">1. Mi ficha <?= $ok($ev['ficha_completa']) ?></h2>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="ficha">
    <?= formularioFicha($ev) ?>
    <p><button class="btn btn-primary" type="submit">Guardar mi ficha</button></p>
  </form>
</div>
<div class="card" style="margin-bottom:14px">
  <h2 style="margin-top:0">2. Test de Genealogía <?= $ok($av['genealogia']) ?></h2>
  <p>Construyes tu árbol y recorres seis temas. Al final pulsa "Guardar mi resultado".</p>
  <a class="btn btn-primary" href="/test-genealogia.php?eval=1"><?= $av['genealogia'] ? 'Repetir' : 'Hacer el test' ?></a>
</div>
<div class="card" style="margin-bottom:14px">
  <h2 style="margin-top:0">3. Test Reconóce TE — Eneatipos <?= $ok($av['eneatipo']) ?></h2>
  <h2 style="margin-top:10px">4. Test Reconóce TE — Heridas de la infancia <?= $ok($av['herida']) ?></h2>
  <p>Son dos partes en la misma página. En cada una, al terminar, pulsa "Guardar mi resultado".</p>
  <a class="btn btn-primary" href="/test-reconocete.php?eval=1"><?= ($av['eneatipo'] && $av['herida']) ? 'Repetir' : 'Hacer el test' ?></a>
</div>
<div class="card">
  <h2 style="margin-top:0">Generar mi informe</h2>
  <?php if ($ev['estado'] === 'completada'): ?>
    <p>Tu informe se envió el <?= h(fechaCorta(substr((string)$ev['completada_en'], 0, 10))) ?>. Si cambiaste algo, puedes generarlo de nuevo.</p>
    <p><a class="btn btn-outline-dark" target="_blank" rel="noopener" href="https://wa.me/<?= h(WHATSAPP_AVC) ?>?text=<?= rawurlencode('Hola Carlos, terminé mi evaluación inicial. Soy ' . $usuario['nombre'] . '. Informe: ' . SITIO_URL . '/informe.php?id=' . (int)$ev['id']) ?>">Avisar a mi terapeuta por WhatsApp</a></p>
  <?php endif; ?>
  <?php if ($todo): ?>
    <form method="post"><input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="finalizar">
      <button class="btn btn-primary" type="submit"><?= $ev['estado'] === 'completada' ? 'Generar de nuevo y reenviar' : 'Generar y enviar mi informe' ?></button></form>
  <?php else: ?>
    <p class="mut">Se habilita cuando la ficha y los tres tests estén listos.</p>
  <?php endif; ?>
</div>
<?php paginaFin();
