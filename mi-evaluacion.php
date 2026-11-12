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
      try { $pdo->prepare("INSERT INTO informes_evaluacion (evaluacion_id, usuario_id, informe) VALUES (?,?,?)")->execute([$ev['id'], $usuario['id'], $inf['html']]); } catch (Throwable $e) { error_log('historial informe: ' . $e->getMessage()); }
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

$hechos = (int)(bool)$ev['ficha_completa'] + (int)$av['genealogia'] + (int)$av['eneatipo'] + (int)$av['herida'];
$pct = (int)round($hechos * 25);
$paso = fn($n, $t, $b, $href) => '<a class="paso-t' . ($b ? ' hecho' : '') . '" href="' . $href . '"><b>' . ($b ? '✓ Listo' : 'Paso ' . $n) . '</b>' . $t . '</a>';

paginaInicio('Mi evaluación');
?>
<div class="hero">
  <p class="mut" style="color:rgba(255,255,255,.7)"><a href="/membresia.php" style="color:#F5C343">Mi membresía</a> · <a href="/cambiar-clave.php" style="color:#F5C343">Cambiar contraseña</a></p>
  <h1>Hola, <?= h(primerNombre($usuario['nombre'])) ?></h1>
  <p>Esta es tu evaluación inicial: una ficha con tus datos y tres tests de autoindagación. Con ella tu terapeuta te acompaña mejor. Puedes ir a tu ritmo y volver cuando quieras: lo que guardes queda en tu historial.</p>
  <div class="barra" style="margin-top:14px;background:rgba(255,255,255,.18)"><i style="width:<?= $pct ?>%"></i></div>
  <p style="font-size:13px;margin:6px 0 0"><?= $hechos ?> de 4 pasos listos</p>
  <div class="pasos">
    <?= $paso(1, 'Mi ficha', $ev['ficha_completa'], '#ficha') ?>
    <?= $paso(2, 'Genealogía', $av['genealogia'], '#genealogia') ?>
    <?= $paso(3, 'Eneatipos', $av['eneatipo'], '#reconocete') ?>
    <?= $paso(4, 'Heridas de la infancia', $av['herida'], '#reconocete') ?>
  </div>
</div>
<?php if (!empty($_GET['confirmado'])) echo aviso('ok', 'Correo confirmado. ¡Bienvenido/a!'); echo $mensaje; ?>
<div class="card" id="ficha" style="margin-bottom:16px">
  <h2 style="margin-top:0"><span class="nbadge<?= $ev['ficha_completa'] ? ' hecho' : '' ?>">1</span>Mi ficha</h2>
  <p class="mut">Cuéntale a tu terapeuta quién eres hoy. Lo que prefieras no compartir, déjalo en blanco (solo edad y síntomas son necesarios).</p>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="ficha">
    <?= formularioFicha($ev) ?>
    <p><button class="btn btn-primary" type="submit">Guardar mi ficha</button></p>
  </form>
</div>
<div class="card" id="genealogia" style="margin-bottom:16px">
  <h2 style="margin-top:0"><span class="nbadge<?= $av['genealogia'] ? ' hecho' : '' ?>">2</span>Test de Genealogía</h2>
  <p>Construyes tu árbol familiar y recorres seis temas. Toma unos 20 a 30 minutos. Al final pulsa "Guardar mi resultado" para volver aquí.</p>
  <a class="btn btn-primary" href="/test-genealogia.php?eval=1"><?= $av['genealogia'] ? 'Repetir el test' : 'Hacer el test' ?></a>
</div>
<div class="card" id="reconocete" style="margin-bottom:16px">
  <h2 style="margin-top:0"><span class="nbadge<?= ($av['eneatipo'] && $av['herida']) ? ' hecho' : '' ?>">3-4</span>Reconóce TE</h2>
  <p>Dos partes en la misma página: tu <strong>eneatipo</strong> <?= $ok($av['eneatipo']) ?> y tus <strong>heridas de la infancia</strong> <?= $ok($av['herida']) ?>. En cada una, al terminar, pulsa "Guardar mi resultado".</p>
  <a class="btn btn-primary" href="/test-reconocete.php?eval=1"><?= ($av['eneatipo'] && $av['herida']) ? 'Repetir los tests' : 'Hacer los tests' ?></a>
</div>
<div class="card" style="border:2px solid <?= $todo ? '#F5C343' : '#eadfc9' ?>">
  <h2 style="margin-top:0"><span class="nbadge<?= $ev['estado'] === 'completada' ? ' hecho' : '' ?>">✓</span>Enviar mi informe</h2>
  <?php if ($ev['estado'] === 'completada'): ?>
    <p>Tu informe se envió el <?= h(fechaCorta(substr((string)$ev['completada_en'], 0, 10))) ?>. Si cambias algo (por ejemplo, para una nueva consulta), puedes generarlo de nuevo: tu historial se conserva.</p>
    <p><a class="btn btn-outline-dark" target="_blank" rel="noopener" href="https://wa.me/<?= h(WHATSAPP_AVC) ?>?text=<?= rawurlencode('Hola Carlos, terminé mi evaluación inicial. Soy ' . $usuario['nombre'] . '. Informe: ' . SITIO_URL . '/informe.php?id=' . (int)$ev['id']) ?>">Avisar a mi terapeuta por WhatsApp</a>
       <a class="btn btn-outline-dark" href="/informe.php?id=<?= (int)$ev['id'] ?>">Ver mi informe</a></p>
  <?php else: ?>
    <p>Cuando completes los cuatro pasos, se genera tu informe y le llega a tu terapeuta por correo.</p>
  <?php endif; ?>
  <?php if ($todo): ?>
    <form method="post"><input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="finalizar">
      <button class="btn btn-primary" type="submit"><?= $ev['estado'] === 'completada' ? 'Generar de nuevo y reenviar' : 'Generar y enviar mi informe' ?></button></form>
  <?php else: ?>
    <p class="mut">Se habilita cuando la ficha y los tres tests estén listos.</p>
  <?php endif; ?>
</div>
<?php paginaFin();
