<?php
// terapeuta.php — panel privado del terapeuta: crea el plan tras una consulta y envía el enlace.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
$terapeuta = requiereTerapeuta();
$pdo = conexionDB();
$mensaje = '';
$nueva = null;

$plantillas = $pdo->query("SELECT id, clave, nombre FROM plantillas_seguimiento ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$catalogo = talleresCatalogo();
$opcionesHeridas = [
  'abandono' => 'Abandono',
  'rechazo' => 'Rechazo',
  'humillacion' => 'Humillación',
  'traicion' => 'Traición',
  'injusticia' => 'Injusticia',
];
$opcionesConflicto = [
  'heridas_infancia' => 'Heridas de infancia',
  'lealtad_ancestro' => 'Lealtad familiar con un ancestro',
  'conflicto_papa' => 'Conflicto con papá',
  'conflicto_mama' => 'Conflicto con mamá',
  'relaciones' => 'Relaciones',
  'salud' => 'Salud',
  'trabajo' => 'Trabajo',
  'salud_mental' => 'Salud mental',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'crear') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $fecha = $_POST['fecha_consulta'] ?? '';
    $plantillaId = (int)($_POST['plantilla_id'] ?? 0);
    $edad = (int)($_POST['edad'] ?? 0);
    $edad = ($edad >= 0 && $edad <= 120 && ($_POST['edad'] ?? '') !== '') ? $edad : null;
    $sexo = in_array($_POST['sexo'] ?? '', ['femenino', 'masculino', 'otro'], true) ? $_POST['sexo'] : null;
    $campoTxt = function ($k, $max) { $v = trim((string)($_POST[$k] ?? '')); return $v === '' ? null : mb_substr($v, 0, $max); };
    $sintomas = $campoTxt('sintomas', 4000);
    $situacion = $campoTxt('situacion_sentimental', 190);
    $hijos = $campoTxt('hijos', 2000);
    $hijosNoNac = $campoTxt('hijos_no_nacidos', 2000);
    $estadoSalud = $campoTxt('estado_salud', 4000);
    $trabajo = $campoTxt('trabajo_actual', 190);
    $profesion = $campoTxt('profesion', 190);
    $conflictos = array_values(array_intersect(array_keys($opcionesConflicto), (array)($_POST['conflictos'] ?? [])));
    $heridas = array_values(array_intersect(array_keys($opcionesHeridas), (array)($_POST['heridas'] ?? [])));
    $detalles = [];
    foreach ($conflictos as $k) {
      $t = trim((string)($_POST['desc'][$k] ?? ''));
      if ($t !== '') $detalles[$k] = mb_substr($t, 0, 2000);
    }
    $conflictoDesc = $detalles ? json_encode($detalles, JSON_UNESCAPED_UNICODE) : null;
    $notas = trim($_POST['notas_privadas'] ?? '');
    $msgTer = trim($_POST['mensaje_terapeuta'] ?? '');
    $talleres = array_values(array_intersect(array_keys($catalogo), (array)($_POST['talleres'] ?? [])));

    $okFecha = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) === 1;
    $plantillaValida = in_array($plantillaId, array_column($plantillas, 'id'), true);

    if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$okFecha || !$plantillaValida) {
      $mensaje = aviso('err', 'Revisa nombre, correo, fecha y plantilla.');
    } else {
      $token = bin2hex(random_bytes(32));
      $expira = date('Y-m-d H:i:s', strtotime('+14 days'));
      $ins = $pdo->prepare(
        "INSERT INTO consultas (terapeuta_usuario_id, nombre, email, whatsapp, fecha_consulta, notas_privadas,
           mensaje_terapeuta, plantilla_id, talleres_recomendados, token, token_expira,
           edad, sexo, sintomas, situacion_sentimental, hijos, hijos_no_nacidos, estado_salud, trabajo_actual, profesion,
           conflictos_emocionales, conflicto_descripcion, heridas_infancia)
         VALUES (?,?,?,?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?,?, ?,?,?)"
      );
      $ins->execute([$terapeuta['id'], $nombre, $email, $whatsapp ?: null, $fecha, $notas ?: null,
        $msgTer ?: null, $plantillaId, implode(',', $talleres) ?: null, $token, $expira,
        $edad, $sexo, $sintomas, $situacion, $hijos, $hijosNoNac, $estadoSalud, $trabajo, $profesion,
        implode(',', $conflictos) ?: null, $conflictoDesc, implode(',', $heridas) ?: null]);
      $nueva = ['id' => (int)$pdo->lastInsertId(), 'nombre' => $nombre, 'email' => $email,
                'whatsapp' => $whatsapp, 'token' => $token];
    }
  }

  if ($accion === 'reenviar') {
    $id = (int)($_POST['consulta_id'] ?? 0);
    $s = $pdo->prepare("SELECT id, nombre, email, whatsapp FROM consultas WHERE id = ? AND terapeuta_usuario_id = ? AND fecha_inicio IS NULL");
    $s->execute([$id, $terapeuta['id']]);
    if ($c = $s->fetch(PDO::FETCH_ASSOC)) {
      $token = bin2hex(random_bytes(32));
      $pdo->prepare("UPDATE consultas SET token = ?, token_expira = ? WHERE id = ?")
          ->execute([$token, date('Y-m-d H:i:s', strtotime('+14 days')), $id]);
      $nueva = $c + ['token' => $token];
    }
  }

  if ($accion === 'correo' && !empty($_POST['consulta_id'])) {
    $id = (int)$_POST['consulta_id'];
    $s = $pdo->prepare("SELECT nombre, email, token FROM consultas WHERE id = ? AND terapeuta_usuario_id = ? AND fecha_inicio IS NULL");
    $s->execute([$id, $terapeuta['id']]);
    if ($c = $s->fetch(PDO::FETCH_ASSOC)) {
      $ok = enviarCorreo($c['email'], $c['nombre'], 'Tu camino de acompañamiento — Aprender a Vivir Contigo',
                         correoInvitacion($c['nombre'], urlInvitacion($c['token'])));
      $mensaje = $ok ? aviso('ok', 'Correo enviado a ' . h($c['email']) . '.')
                     : aviso('err', 'No se pudo enviar el correo. Revisa BREVO_API_KEY y que el remitente esté verificado en Brevo; puedes enviar el enlace por WhatsApp.');
    }
  }

  if ($accion === 'finalizar') {
    $pdo->prepare("UPDATE consultas SET estado = 'finalizada' WHERE id = ? AND terapeuta_usuario_id = ?")
        ->execute([(int)($_POST['consulta_id'] ?? 0), $terapeuta['id']]);
  }
}

// Tras crear/reenviar: tarjeta con el enlace, botón de correo y de WhatsApp.
$tarjetaEnlace = '';
if ($nueva) {
  $link = urlInvitacion($nueva['token']);
  $textoWa = 'Hola ' . primerNombre($nueva['nombre']) . ', gracias por tu consulta. Preparé tu camino de acompañamiento con ejercicios diarios. '
           . 'Para activarlo crea tu cuenta aquí (el enlace es personal y vence en 14 días): ' . $link;
  $tarjetaEnlace = '<div class="card" style="margin:18px 0"><h3>Enlace listo para ' . h($nueva['nombre']) . '</h3>'
    . '<p class="mut">Pégalo o envíalo con los botones. Quien lo abra deberá registrarse con su nombre.</p>'
    . '<input class="campo" readonly value="' . h($link) . '" onclick="this.select()">'
    . '<div class="fila" style="margin-top:12px">'
    . '<form method="post"><input type="hidden" name="csrf" value="' . h(tokenCsrf()) . '"><input type="hidden" name="accion" value="correo">'
    . '<input type="hidden" name="consulta_id" value="' . (int)$nueva['id'] . '"><button class="btn btn-primary">Enviar por correo</button></form>';
  if (!empty($nueva['whatsapp'])) {
    $tarjetaEnlace .= '<a class="btn btn-outline-dark" target="_blank" rel="noopener" href="' . h(linkWhatsApp($nueva['whatsapp'], $textoWa)) . '">Abrir WhatsApp con el mensaje</a>';
  }
  $tarjetaEnlace .= '</div></div>';
}

$lista = $pdo->prepare(
  "SELECT c.id, c.nombre, c.email, c.whatsapp, c.fecha_consulta, c.estado, c.fecha_inicio, c.token, c.token_expira,
          c.usuario_id, p.nombre AS plantilla,
          (SELECT COUNT(*) FROM plantilla_ejercicios e WHERE e.plantilla_id = c.plantilla_id) AS total,
          (SELECT COUNT(*) FROM progreso_ejercicios g WHERE g.consulta_id = c.id) AS hechos
   FROM consultas c JOIN plantillas_seguimiento p ON p.id = c.plantilla_id
   WHERE c.terapeuta_usuario_id = ? ORDER BY c.id DESC LIMIT 100"
);
$lista->execute([$terapeuta['id']]);
$consultas = $lista->fetchAll(PDO::FETCH_ASSOC);

$ficha = null;
if (isset($_GET['ver'])) {
  $f = $pdo->prepare("SELECT c.*, p.nombre AS plantilla FROM consultas c JOIN plantillas_seguimiento p ON p.id = c.plantilla_id
                      WHERE c.id = ? AND c.terapeuta_usuario_id = ?");
  $f->execute([(int)$_GET['ver'], $terapeuta['id']]);
  $ficha = $f->fetch(PDO::FETCH_ASSOC) ?: null;
}

paginaInicio('Panel del terapeuta');
?>
<h1>Panel del terapeuta</h1>
<p class="mut">Sesión de <?= h($terapeuta['nombre']) ?> · <a href="/membresia.php">Volver a la membresía</a> · <a href="/cambiar-clave.php">Cambiar contraseña</a></p>
<?= $mensaje ?>
<?= $tarjetaEnlace ?>
<?php if ($ficha):
  $detalleTxt = '';
  $dj = json_decode((string)$ficha['conflicto_descripcion'], true);
  if (is_array($dj)) {
    foreach ($dj as $k => $t) $detalleTxt .= ($opcionesConflicto[$k] ?? $k) . ': ' . $t . "\n\n";
  } elseif (!empty($ficha['conflicto_descripcion'])) {
    $detalleTxt = (string)$ficha['conflicto_descripcion'];
  }
  $detalleTxt = trim($detalleTxt);
  $filas = [
    'Edad' => $ficha['edad'], 'Sexo' => $ficha['sexo'], 'Síntomas' => $ficha['sintomas'],
    'Situación sentimental' => $ficha['situacion_sentimental'], 'Hijos y nombres' => $ficha['hijos'],
    'Hijos no nacidos o muertos al nacer' => $ficha['hijos_no_nacidos'], 'Estado de salud' => $ficha['estado_salud'],
    'Trabajo actual' => $ficha['trabajo_actual'], 'Profesión' => $ficha['profesion'],
    'Conflicto emocional no resuelto' => implode(', ', array_map(fn($k) => $opcionesConflicto[$k] ?? $k, array_filter(explode(',', (string)$ficha['conflictos_emocionales'])))),
    'Heridas de infancia' => implode(', ', array_map(fn($k) => $opcionesHeridas[$k] ?? $k, array_filter(explode(',', (string)$ficha['heridas_infancia'])))),
    'Descripción por conflicto' => $detalleTxt,
    'Notas privadas' => $ficha['notas_privadas'],
  ]; ?>
<div class="card" style="margin:18px 0">
  <h2>Ficha de <?= h($ficha['nombre']) ?></h2>
  <p class="mut"><?= h($ficha['email']) ?> · Consulta del <?= h(fechaCorta($ficha['fecha_consulta'])) ?> · Plantilla: <?= h($ficha['plantilla']) ?></p>
  <table class="tabla">
    <?php foreach ($filas as $k => $v): ?>
      <tr><th style="width:38%"><?= h($k) ?></th><td><?= ($v === null || $v === '') ? '<span class="mut">—</span>' : nl2br(h($v)) ?></td></tr>
    <?php endforeach; ?>
  </table>
  <p><a href="/terapeuta.php">Cerrar ficha</a></p>
</div>
<?php endif; ?>


<div class="card">
  <h2>Nueva consulta</h2>
  <form method="post" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
    <input type="hidden" name="accion" value="crear">
    <label class="etq" for="nombre">Nombre de la persona</label>
    <input class="campo" id="nombre" name="nombre" required maxlength="150">
    <label class="etq" for="email">Correo</label>
    <input class="campo" id="email" name="email" type="email" required maxlength="190">
    <label class="etq" for="whatsapp">WhatsApp (con o sin 57)</label>
    <input class="campo" id="whatsapp" name="whatsapp" inputmode="tel" maxlength="30">
    <label class="etq" for="fecha">Fecha de la consulta</label>
    <input class="campo" id="fecha" name="fecha_consulta" type="date" value="<?= h(hoy()) ?>" required>

    <h3 style="margin-top:26px">Ficha de la consulta</h3>
    <p class="mut">Solo tú ves estos datos. No se envían por correo ni por WhatsApp, ni los ve la persona.</p>
    <div class="fila">
      <div style="flex:1;min-width:120px"><label class="etq" for="edad">Edad</label>
        <input class="campo" id="edad" name="edad" type="number" min="0" max="120" inputmode="numeric"></div>
      <div style="flex:2;min-width:160px"><label class="etq" for="sexo">Sexo</label>
        <select class="campo" id="sexo" name="sexo"><option value="">—</option>
          <option value="femenino">Femenino</option><option value="masculino">Masculino</option><option value="otro">Otro</option></select></div>
    </div>
    <label class="etq" for="sintomas">Síntomas</label>
    <textarea class="campo" id="sintomas" name="sintomas" rows="3" maxlength="4000"></textarea>
    <label class="etq" for="situacion_sentimental">Situación sentimental actual</label>
    <input class="campo" id="situacion_sentimental" name="situacion_sentimental" maxlength="190" placeholder="Ej.: casada, separada hace 2 años, soltero…">
    <label class="etq" for="hijos">Hijos y sus nombres</label>
    <textarea class="campo" id="hijos" name="hijos" rows="2" maxlength="2000" placeholder="Ej.: Ana (12), Luis (8)"></textarea>
    <label class="etq" for="hijos_no_nacidos">Hijos no nacidos o muertos al nacer</label>
    <textarea class="campo" id="hijos_no_nacidos" name="hijos_no_nacidos" rows="2" maxlength="2000" placeholder="Abortos, pérdidas gestacionales, hijos fallecidos al nacer…"></textarea>
    <label class="etq" for="estado_salud">Estado de salud</label>
    <textarea class="campo" id="estado_salud" name="estado_salud" rows="3" maxlength="4000"></textarea>
    <div class="fila">
      <div style="flex:1;min-width:200px"><label class="etq" for="trabajo_actual">Trabajo actual</label>
        <input class="campo" id="trabajo_actual" name="trabajo_actual" maxlength="190"></div>
      <div style="flex:1;min-width:200px"><label class="etq" for="profesion">Profesión</label>
        <input class="campo" id="profesion" name="profesion" maxlength="190"></div>
    </div>

    <span class="etq" style="margin-top:22px">Conflicto emocional no resuelto</span>
    <p class="mut" style="margin-top:0">Marca los que apliquen; al marcar cada uno se abre su casilla para describirlo.</p>
    <?php foreach ($opcionesConflicto as $clave => $etiqueta): ?>
      <div style="border:1px solid #eadfc9;border-radius:10px;padding:10px 14px;margin:8px 0;background:#fff">
        <label><input type="checkbox" name="conflictos[]" value="<?= h($clave) ?>" data-abre="det-<?= h($clave) ?>"> <strong><?= h($etiqueta) ?></strong></label>
        <div id="det-<?= h($clave) ?>" style="display:none;margin-top:8px">
          <?php if ($clave === 'heridas_infancia'): ?>
            <span class="mut">Herida(s) de infancia (puedes marcar varias):</span><br>
            <?php foreach ($opcionesHeridas as $hk => $hEt): ?>
              <label style="display:inline-block;margin:4px 14px 4px 0"><input type="checkbox" name="heridas[]" value="<?= h($hk) ?>"> <?= h($hEt) ?></label>
            <?php endforeach; ?>
          <?php endif; ?>
          <textarea class="campo" name="desc[<?= h($clave) ?>]" rows="3" maxlength="2000" style="margin-top:8px"
            placeholder="Describe: <?= h(mb_strtolower($etiqueta)) ?> — qué lo dispara, desde cuándo, a quién involucra…"></textarea>
        </div>
      </div>
    <?php endforeach; ?>

    <label class="etq" for="plantilla">Plantilla de ejercicios diarios</label>
    <select class="campo" id="plantilla" name="plantilla_id" required>
      <?php foreach ($plantillas as $p): ?>
        <option value="<?= (int)$p['id'] ?>"><?= h($p['nombre']) ?></option>
      <?php endforeach; ?>
    </select>

    <span class="etq">Talleres que le recomiendas según lo evidenciado</span>
    <?php foreach ($catalogo as $clave => $t): ?>
      <label style="display:block;margin:6px 0"><input type="checkbox" name="talleres[]" value="<?= h($clave) ?>">
        <strong><?= h($t['nombre']) ?></strong> <span class="mut">— <?= h($t['resumen']) ?></span></label>
    <?php endforeach; ?>

    <label class="etq" for="msg">Mensaje para la persona (lo verá en Mi camino)</label>
    <textarea class="campo" id="msg" name="mensaje_terapeuta" rows="4" maxlength="2000"
      placeholder="Ej.: Lo que conversamos hoy abre un camino de reconciliación con tu historia. Ve a tu ritmo."></textarea>

    <label class="etq" for="notas">Notas privadas (solo tú las ves)</label>
    <textarea class="campo" id="notas" name="notas_privadas" rows="3" maxlength="4000"></textarea>
    <p class="mut">Mantén estas notas breves y no las incluyas en correos o WhatsApp: son datos sensibles de la persona.</p>

    <p style="margin-top:16px"><button class="btn btn-primary" type="submit">Crear plan y generar enlace</button></p>
  </form>
</div>

<div class="card" style="margin-top:24px">
  <h2>Consultas recientes</h2>
  <?php if (!$consultas): ?>
    <p class="mut">Aún no has creado ninguna.</p>
  <?php else: ?>
  <table class="tabla">
    <tr><th>Persona</th><th>Plantilla</th><th>Estado</th><th>Avance</th><th></th></tr>
    <?php foreach ($consultas as $c):
      $pendiente = $c['fecha_inicio'] === null;
      $vencido = $pendiente && strtotime($c['token_expira']) < time();
    ?>
    <tr>
      <td><strong><?= h($c['nombre']) ?></strong><br><span class="mut"><?= h($c['email']) ?> · <?= h(fechaCorta($c['fecha_consulta'])) ?></span></td>
      <td><?= h($c['plantilla']) ?></td>
      <td><?= $pendiente ? ($vencido ? '<span class="chip">Enlace vencido</span>' : '<span class="chip">Esperando registro</span>')
                          : '<span class="chip">' . h($c['estado'] === 'finalizada' ? 'Finalizada' : 'En proceso') . '</span>' ?></td>
      <td><?= $pendiente ? '—' : (int)$c['hechos'] . ' / ' . (int)$c['total'] ?></td>
      <td>
        <a href="?ver=<?= (int)$c['id'] ?>">Ver ficha</a><br>
        <?php if ($pendiente): ?>
          <form method="post"><input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
            <input type="hidden" name="accion" value="reenviar"><input type="hidden" name="consulta_id" value="<?= (int)$c['id'] ?>">
            <button class="btn btn-outline-dark" type="submit">Nuevo enlace</button></form>
        <?php elseif ($c['estado'] !== 'finalizada'): ?>
          <form method="post"><input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>">
            <input type="hidden" name="accion" value="finalizar"><input type="hidden" name="consulta_id" value="<?= (int)$c['id'] ?>">
            <button class="btn btn-outline-dark" type="submit">Finalizar</button></form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
<script>
document.querySelectorAll('input[data-abre]').forEach(function (cb) {
  function ver() { var el = document.getElementById(cb.dataset.abre); if (el) el.style.display = cb.checked ? 'block' : 'none'; }
  cb.addEventListener('change', ver); ver();
});
</script>
<?php paginaFin();
