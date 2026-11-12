<?php
// portal-terapeuta.php — portal del terapeuta: enviar enlaces de evaluación temporales y revisar las evaluaciones.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/enlaces-evaluacion.php';

// Acceso: sin sesión -> ingresar y volver aquí; con sesión pero sin rol de terapeuta -> aviso claro.
if (empty($_SESSION['usuario_id'])) { header('Location: /login.html?next=' . urlencode('/portal-terapeuta.php')); exit; }
$terapeuta = usuarioActual();
if (!$terapeuta || ($terapeuta['rol'] ?? '') !== 'terapeuta') {
  http_response_code(403);
  paginaInicio('Acceso restringido');
  echo '<div class="card"><h1>Acceso solo para el terapeuta</h1><p>Esta cuenta (' . h($terapeuta['email'] ?? '') . ') no tiene permisos de terapeuta.</p>'
     . '<p><a class="btn btn-primary" href="/membresia.php">Ir a mi cuenta</a></p></div>';
  paginaFin(); exit;
}
$pdo = conexionDB();
$tid = (int)$terapeuta['id'];

$VIGENCIAS = ['+24 hours' => '24 horas', '+3 days' => '3 días', '+7 days' => '7 días', '+30 days' => '30 días'];
$USOS = [1 => 'Una persona', 5 => 'Hasta 5 personas', 20 => 'Hasta 20 personas'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verificaCsrf();
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'crear') {
    $nom = trim($_POST['nombre'] ?? ''); $mail = trim($_POST['correo'] ?? ''); $cel = trim($_POST['celular'] ?? '');
    $vig = $_POST['vigencia'] ?? '+3 days'; if (!isset($VIGENCIAS[$vig])) $vig = '+3 days';
    $usos = (int)($_POST['usos'] ?? 1); if (!isset($USOS[$usos])) $usos = 1;
    if ($mail !== '' && !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['flash'] = ['err', 'El correo no es válido.'];
    } else {
      $tok = bin2hex(random_bytes(32));
      $expira = ahoraBogota($vig);
      $sel = array_values(array_intersect(array_keys(partesEvaluacionCatalogo()), (array)($_POST['tests'] ?? [])));
      if (!$sel) $sel = ['genealogia', 'eneatipo', 'herida'];
      $testsCsv = implode(',', $sel);
      try {
        $pdo->prepare("INSERT INTO enlaces_evaluacion (terapeuta_usuario_id, token, nombre_destino, correo_destino, celular_destino, expira_en, usos_max, tests) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$tid, $tok, $nom ?: null, $mail ?: null, $cel ?: null, $expira, $usos, $testsCsv]);
      } catch (PDOException $e) { // aún sin migracion-tests-eval.sql: se crea con las tres de siempre
        $pdo->prepare("INSERT INTO enlaces_evaluacion (terapeuta_usuario_id, token, nombre_destino, correo_destino, celular_destino, expira_en, usos_max) VALUES (?,?,?,?,?,?,?)")
            ->execute([$tid, $tok, $nom ?: null, $mail ?: null, $cel ?: null, $expira, $usos]);
      }
      $nombresTests = implode(', ', array_map(fn($k) => partesEvaluacionCatalogo()[$k], $sel));
      $lnk = SITIO_URL . '/bienvenida.php?t=' . $tok;
      $primer = $nom !== '' ? explode(' ', $nom)[0] : '';
      $msg = 'Enlace creado (' . $nombresTests . '): vence ' . textoVence($expira) . '.';
      if ($mail !== '' && !empty($_POST['enviar_correo'])) {
        $ok = enviarCorreo($mail, $nom ?: $mail, 'Tu evaluación inicial — Aprender a Vivir Contigo',
          '<h2 style="margin-top:0;">Hola' . ($primer ? ', ' . textoSeguro($primer) : '') . '</h2>'
          . '<p>Gracias por pedir tu terapia: dar este paso es un acto de valentía. Entra con el botón: ahí te doy la bienvenida y te cuento cómo será el proceso. Para prepararlo, necesito que completes tu evaluación inicial:</p>'
          . '<ol><li><strong>Crea tu cuenta</strong> (nombre completo, correo, celular y contraseña).</li>'
          . '<li><strong>Confirma tu correo</strong> con el mensaje que te llegará.</li>'
          . '<li><strong>Llena tu ficha</strong> y <strong>haz tus tests</strong>: ' . textoSeguro($nombresTests) . '.</li></ol>'
          . '<p>Puedes hacerlo a tu ritmo; lo que respondas solo lo veo yo.</p>'
          . botonCorreo($lnk, 'Leer mi bienvenida y empezar')
          . '<p style="font-size:13px;color:#6b6480">Este enlace es personal y vence el ' . date('d/m/Y \a \l\a\s H:i', strtotime($expira)) . '.</p>');
        $msg .= $ok ? ' Correo enviado a ' . $mail . '.' : ' No se pudo enviar el correo: envía el enlace por WhatsApp.';
      }
      $_SESSION['flash'] = ['ok', $msg];
      $_SESSION['flash_nuevo'] = ['lnk' => $lnk, 'nombre' => $nom, 'cel' => $cel, 'expira' => $expira, 'tests' => $nombresTests];
    }
  } elseif ($accion === 'revocar') {
    $pdo->prepare("UPDATE enlaces_evaluacion SET activo = 0 WHERE id = ? AND terapeuta_usuario_id = ?")->execute([(int)($_POST['id'] ?? 0), $tid]);
    $_SESSION['flash'] = ['ok', 'Enlace desactivado: ya no funciona.'];
  } elseif ($accion === 'extender') {
    $pdo->prepare("UPDATE enlaces_evaluacion SET activo = 1, expira_en = ? WHERE id = ? AND terapeuta_usuario_id = ? AND usos < usos_max")
        ->execute([ahoraBogota('+3 days'), (int)($_POST['id'] ?? 0), $tid]);
    $_SESSION['flash'] = ['ok', 'Enlace reactivado por 3 días más.'];
  }
  header('Location: /portal-terapeuta.php'); exit;
}
$flash = $_SESSION['flash'] ?? null; $nuevo = $_SESSION['flash_nuevo'] ?? null;
unset($_SESSION['flash'], $_SESSION['flash_nuevo']);

// Enlaces recientes
$q = $pdo->prepare("SELECT * FROM enlaces_evaluacion WHERE terapeuta_usuario_id = ? ORDER BY id DESC LIMIT 30");
$q->execute([$tid]); $enlaces = $q->fetchAll(PDO::FETCH_ASSOC);

// Evaluaciones
$l = $pdo->prepare("SELECT e.id, e.estado, e.creado_en, e.completada_en, u.nombre, u.email, u.telefono, u.email_verificado, u.fecha_vencimiento
                    FROM evaluaciones e JOIN usuarios u ON u.id = e.usuario_id WHERE e.terapeuta_usuario_id = ? ORDER BY e.id DESC LIMIT 200");
$l->execute([$tid]); $lista = $l->fetchAll(PDO::FETCH_ASSOC);
$tot = count($lista); $comp = 0; $curso = 0; $sinConf = 0;
foreach ($lista as $r) {
  if ($r['estado'] === 'completada') $comp++;
  elseif ((int)$r['email_verificado'] !== 1) $sinConf++;
  else $curso++;
}
$activos = 0; foreach ($enlaces as $e) if (estadoEnlace($e) === 'activo') $activos++;

paginaInicio('Portal del terapeuta');
?>
<style>
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:0 0 18px}
.stat{border-radius:16px;padding:16px;text-align:center;color:#211A33}
.stat b{display:block;font-family:Poppins,Arial,sans-serif;font-size:2rem;line-height:1.1}
.stat span{font-size:14px}
.tp-hero{background:linear-gradient(135deg,#211A33,#4a2c5a 60%,#C2607D);color:#fff;border-radius:20px;padding:24px;margin:6px 0 18px}
.tp-hero h1{color:#fff;margin:0 0 4px;font-size:1.6rem}.tp-hero p{margin:0;color:rgba(255,255,255,.85)}
.tp-hero a{color:#F5C343}
.est{display:inline-block;border-radius:20px;padding:3px 12px;font-size:13px;font-family:Poppins,Arial,sans-serif;font-weight:600}
.e-activo{background:#dff5e5;color:#17663a}.e-vencido{background:#fde8e8;color:#8a1f1f}.e-usado{background:#fff1cf;color:#8a5a00}.e-revocado{background:#e8e5ef;color:#4a4360}
.tabla td,.tabla th{vertical-align:top}
.tchip{cursor:pointer;display:inline-block}
.tchip input{position:absolute;opacity:0;pointer-events:none}
.tchip span{display:inline-block;border:2px solid #d9cfe6;background:#fff;color:#211A33;border-radius:30px;padding:10px 20px;font-family:Poppins,Arial,sans-serif;font-weight:600;font-size:15px;transition:.15s}
.tchip.on span{background:#211A33;border-color:#211A33;color:#F5C343}
.tchip input:focus-visible + span{outline:3px solid #F5C343}
@media(max-width:700px){.stats{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="tp-hero">
  <h1>Portal del terapeuta</h1>
  <p>Hola, <?= h(primerNombre($terapeuta['nombre'])) ?>. Aquí envías evaluaciones y revisas lo que llega.
     <a href="/terapeuta.php">Planes y consultas</a> · <a href="/cambiar-clave.php">Cambiar contraseña</a> · <a href="/membresia.php">Mi cuenta</a></p>
</div>

<div class="stats">
  <div class="stat" style="background:#fff3f6"><b><?= $tot ?></b><span>Evaluaciones</span></div>
  <div class="stat" style="background:#dff5e5"><b><?= $comp ?></b><span>Completadas</span></div>
  <div class="stat" style="background:#fff6d9"><b><?= $curso + $sinConf ?></b><span>En proceso</span></div>
  <div class="stat" style="background:#ece6f7"><b><?= $activos ?></b><span>Enlaces activos</span></div>
</div>

<?php if ($flash) echo aviso($flash[0], h($flash[1])); ?>

<?php if ($nuevo): $txt = ($nuevo['nombre'] ? 'Hola ' . explode(' ', $nuevo['nombre'])[0] . ', ' : 'Hola, ') . "soy Carlos de Aprender a Vivir Contigo. Para tu proceso, entra a este enlace personal, crea tu cuenta y llena tu evaluación inicial (una ficha y estos tests: " . ($nuevo['tests'] ?? '') . "). El enlace vence el " . date('d/m/Y H:i', strtotime($nuevo['expira'])) . ":\n" . $nuevo['lnk']; ?>
<div class="card" style="margin-bottom:16px;border:2px solid #F5C343;background:#fffbea">
  <h2 style="margin-top:0">Tu enlace está listo</h2>
  <input class="campo" id="nuevo-enlace" readonly value="<?= h($nuevo['lnk']) ?>" onclick="this.select()">
  <p class="fila" style="margin-top:10px">
    <button class="btn btn-outline-dark" type="button" onclick="navigator.clipboard.writeText(document.getElementById('nuevo-enlace').value);this.textContent='¡Copiado!'">Copiar enlace</button>
    <a class="btn btn-primary" target="_blank" rel="noopener" href="<?= h($nuevo['cel'] ? linkWhatsApp($nuevo['cel'], $txt) : 'https://wa.me/?text=' . rawurlencode($txt)) ?>">Enviar por WhatsApp</a>
  </p>
</div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Enviar una evaluación</h2>
  <p class="mut">Cada enlace es temporal: vence solo y se puede desactivar cuando quieras.</p>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="crear">
    <div class="fila">
      <div style="flex:1;min-width:180px"><label class="etq" for="n-nom">Nombre (opcional)</label><input class="campo" id="n-nom" name="nombre" maxlength="150"></div>
      <div style="flex:1;min-width:200px"><label class="etq" for="n-mail">Correo (opcional)</label><input class="campo" id="n-mail" name="correo" type="email" maxlength="190"></div>
      <div style="flex:1;min-width:160px"><label class="etq" for="n-cel">Celular (opcional)</label><input class="campo" id="n-cel" name="celular" type="tel" maxlength="30"></div>
    </div>
    <div style="margin-top:12px">
      <span class="etq">Tests que enviarás (toca para elegir)</span>
      <div class="fila" id="chips-tests" style="gap:10px;margin-top:6px">
        <?php foreach (partesEvaluacionCatalogo() as $k => $t): $on = in_array($k, ['genealogia', 'eneatipo', 'herida'], true); ?>
          <label class="tchip<?= $on ? ' on' : '' ?>"><input type="checkbox" name="tests[]" value="<?= h($k) ?>"<?= $on ? ' checked' : '' ?>><span><?= h($t) ?></span></label>
        <?php endforeach; ?>
      </div>
      <p class="mut" style="margin:6px 0 0;font-size:13px">La persona solo verá y hará los tests que elijas aquí. La ficha siempre va incluida.</p>
    </div>
    <div class="fila" style="margin-top:8px">
      <div style="flex:1;min-width:180px"><label class="etq" for="n-vig">El enlace vence en</label>
        <select class="campo" id="n-vig" name="vigencia"><?php foreach ($VIGENCIAS as $v => $t): ?><option value="<?= h($v) ?>"<?= $v === '+3 days' ? ' selected' : '' ?>><?= h($t) ?></option><?php endforeach; ?></select></div>
      <div style="flex:1;min-width:180px"><label class="etq" for="n-usos">Lo puede usar</label>
        <select class="campo" id="n-usos" name="usos"><?php foreach ($USOS as $v => $t): ?><option value="<?= $v ?>"><?= h($t) ?></option><?php endforeach; ?></select></div>
    </div>
    <label style="display:flex;gap:10px;margin:12px 0;align-items:center"><input type="checkbox" name="enviar_correo" value="1" checked> <span>Enviar también por correo (si escribí un correo)</span></label>
    <button class="btn btn-primary" type="submit" style="font-size:1.05rem;padding:13px 26px">Crear enlace temporal</button>
  </form>
</div>

<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Evaluaciones recibidas</h2>
  <?php if (!$lista): ?><p class="mut">Aún no hay evaluaciones. Cuando alguien se registre con tu enlace, aparecerá aquí.</p><?php else: ?>
  <div style="overflow-x:auto;-webkit-overflow-scrolling:touch"><table class="tabla" style="min-width:560px">
    <tr><th>Persona</th><th>Estado</th><th>Fecha</th><th>Acceso al club</th><th></th></tr>
    <?php foreach ($lista as $r):
      $estado = $r['estado'] === 'completada' ? 'Completada' : ((int)$r['email_verificado'] !== 1 ? 'Correo sin confirmar' : ($r['estado'] === 'en_curso' ? 'En curso' : 'Registrada')); ?>
    <tr>
      <td><strong><?= h($r['nombre']) ?></strong><br><span class="mut"><?= h($r['email']) ?> · <?= h($r['telefono']) ?></span></td>
      <td><span class="chip"><?= h($estado) ?></span></td>
      <td class="mut"><?= h(substr((string)($r['completada_en'] ?: $r['creado_en']), 0, 10)) ?></td>
      <td class="mut"><?= !empty($r['fecha_vencimiento']) ? (strtotime($r['fecha_vencimiento']) >= time() ? 'hasta ' : 'terminó ') . h(date('d/m/Y', strtotime($r['fecha_vencimiento']))) : '—' ?></td>
      <td><a class="btn btn-primary" href="/consultante.php?id=<?= (int)$r['id'] ?>">Ver consultante</a>
        <?php if ($r['estado'] === 'completada'): ?> <a class="btn btn-outline-dark" href="/informe.php?id=<?= (int)$r['id'] ?>">Informe</a><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php endif; ?>
</div>

<div class="card">
  <h2 style="margin-top:0">Enlaces enviados</h2>
  <?php if (!$enlaces): ?><p class="mut">Aún no has creado enlaces.</p><?php else: ?>
  <div style="overflow-x:auto;-webkit-overflow-scrolling:touch"><table class="tabla" style="min-width:560px">
    <tr><th>Para</th><th>Tests</th><th>Estado</th><th>Vence</th><th>Usos</th><th></th></tr>
    <?php foreach ($enlaces as $e): $st = estadoEnlace($e); ?>
    <tr>
      <td><?= $e['nombre_destino'] || $e['correo_destino'] ? '<strong>' . h($e['nombre_destino'] ?: $e['correo_destino']) . '</strong>' . ($e['nombre_destino'] && $e['correo_destino'] ? '<br><span class="mut">' . h($e['correo_destino']) . '</span>' : '') : '<span class="mut">Enlace sin destinatario</span>' ?>
        <br><span class="mut" style="font-size:13px">Creado <?= h(date('d/m H:i', strtotime($e['creado_en']))) ?></span></td>
      <td class="mut" style="font-size:13px"><?= h(implode(', ', array_map(fn($k) => partesEvaluacionCatalogo()[$k], partesDeEvaluacion($e)))) ?></td>
      <td><span class="est e-<?= h($st) ?>"><?= h(ucfirst($st)) ?></span></td>
      <td class="mut"><?= h(textoVence($e['expira_en'])) ?></td>
      <td class="mut"><?= (int)$e['usos'] ?> / <?= (int)$e['usos_max'] ?></td>
      <td>
        <?php if ($st === 'activo'): $lk = SITIO_URL . '/bienvenida.php?t=' . $e['token']; ?>
          <button class="btn btn-outline-dark" type="button" onclick="navigator.clipboard.writeText('<?= h($lk) ?>');this.textContent='¡Copiado!'">Copiar</button>
          <a class="btn btn-outline-dark" target="_blank" rel="noopener" href="https://wa.me/?text=<?= rawurlencode("Hola, soy Carlos de Aprender a Vivir Contigo. Entra a este enlace personal para tu evaluación inicial:\n" . $lk) ?>">WhatsApp</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Este enlace dejará de funcionar. ¿Desactivarlo?')">
            <input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="revocar"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
            <button class="btn btn-outline-dark" type="submit">Desactivar</button></form>
        <?php elseif ($st === 'vencido' || $st === 'revocado'): if ((int)$e['usos'] < (int)$e['usos_max']): ?>
          <form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?= h(tokenCsrf()) ?>"><input type="hidden" name="accion" value="extender"><input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
            <button class="btn btn-outline-dark" type="submit">Reactivar 3 días</button></form>
        <?php endif; endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table></div>
  <?php endif; ?>
</div>
<script>
document.querySelectorAll('#chips-tests .tchip').forEach(function (l) {
  var c = l.querySelector('input');
  c.addEventListener('change', function () { l.classList.toggle('on', c.checked); });
});
document.querySelector('#chips-tests').closest('form').addEventListener('submit', function (e) {
  if (!this.querySelector('#chips-tests input:checked')) { e.preventDefault(); alert('Elige al menos un test.'); }
});
</script>
<?php paginaFin();
