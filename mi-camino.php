<?php
// mi-camino.php — registro y avance del consultante: ejercicios diarios y talleres.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
requiereLogin();
$pdo = conexionDB();
$usuario = usuarioActual();
if (!$usuario) { header('Location: /login.html'); exit; }

$consultas = consultasDeUsuario($pdo, $usuario);
$bases = basesCatalogo();
$catalogo = talleresCatalogo();
$estados = estadosTaller();

paginaInicio('Mi camino');
echo '<p class="mut"><a href="/membresia.php">← Mi membresía</a></p>';
echo '<h1>Mi camino, ' . h(primerNombre($usuario['nombre'])) . '</h1>';
if (!empty($_GET['bienvenida'])) echo aviso('ok', 'Tu cuenta está lista. Aquí verás tus ejercicios de cada día y tus talleres.');

if (!$consultas) {
  echo '<div class="card"><p>Todavía no tienes un plan de acompañamiento activo. Después de tu consulta recibirás un enlace por correo o WhatsApp para activarlo.</p>'
     . '<p><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, quiero agendar una consulta.') . '">Agendar consulta por WhatsApp</a></p></div>';
  paginaFin();
  exit;
}

$sel = (int)($_GET['c'] ?? $consultas[0]['id']);
$consulta = $consultas[0];
foreach ($consultas as $c) if ((int)$c['id'] === $sel) $consulta = $c;

$e = $pdo->prepare("SELECT * FROM plantilla_ejercicios WHERE plantilla_id = ? ORDER BY dia, id");
$e->execute([$consulta['plantilla_id']]);
$ejercicios = $e->fetchAll(PDO::FETCH_ASSOC);

$g = $pdo->prepare("SELECT ejercicio_id, nota FROM progreso_ejercicios WHERE consulta_id = ?");
$g->execute([$consulta['id']]);
$hechos = [];
foreach ($g->fetchAll(PDO::FETCH_ASSOC) as $f) $hechos[(int)$f['ejercicio_id']] = $f['nota'];

$total = count($ejercicios);
$nHechos = count($hechos);
$pct = $total ? (int)round($nHechos * 100 / $total) : 0;
$diaHoy = diaActual($consulta['fecha_inicio']);
$hayButeyko = false;
foreach ($ejercicios as $x) if ($x['base'] === 'buteyko') $hayButeyko = true;

if (count($consultas) > 1) {
  echo '<p class="fila">';
  foreach ($consultas as $c) {
    echo '<a class="chip" href="?c=' . (int)$c['id'] . '">' . h($c['plantilla_nombre']) . ' · ' . h(fechaCorta($c['fecha_consulta'])) . '</a>';
  }
  echo '</p>';
}
?>
<div class="card">
  <h2><?= h($consulta['plantilla_nombre']) ?></h2>
  <p class="mut">Inicio: <?= h(fechaCorta($consulta['fecha_inicio'])) ?> · Día <?= max(1, min($diaHoy, $total ?: 1)) ?> de <?= (int)($total ? max(array_column($ejercicios, 'dia')) : 0) ?></p>
  <div class="barra" aria-hidden="true"><i id="barra" style="width:<?= $pct ?>%"></i></div>
  <p><strong id="conteo"><?= $nHechos ?> de <?= $total ?></strong> ejercicios hechos</p>
  <?php if (!empty($consulta['mensaje_terapeuta'])): ?>
    <blockquote style="margin:14px 0;padding:12px 16px;border-left:4px solid #F5C343;background:#fff"><?= nl2br(h($consulta['mensaje_terapeuta'])) ?><br><span class="mut">— Carlos</span></blockquote>
  <?php endif; ?>
</div>

<h2 style="margin-top:28px">Mis ejercicios</h2>
<?php foreach ($ejercicios as $x):
  $dia = (int)$x['dia'];
  $abierto = $dia <= $diaHoy;
  $hecho = array_key_exists((int)$x['id'], $hechos);
?>
  <div class="card" style="margin:12px 0;<?= $abierto ? '' : 'opacity:.55' ?>">
    <div class="ej">
      <input type="checkbox" data-ej="<?= (int)$x['id'] ?>" <?= $hecho ? 'checked' : '' ?> <?= $abierto ? '' : 'disabled' ?> aria-label="Marcar como hecho">
      <div>
        <p class="mut" style="margin:0">Día <?= $dia ?> · <?= h($bases[$x['base']] ?? '') ?><?= $x['duracion_min'] ? ' · ' . (int)$x['duracion_min'] . ' min' : '' ?>
          <?= $dia === $diaHoy ? ' · <strong>Hoy</strong>' : '' ?></p>
        <h3 style="margin:4px 0 8px"><?= h($x['titulo']) ?></h3>
        <?php if ($abierto): ?>
          <p><?= nl2br(h($x['instrucciones'])) ?></p>
        <?php else: ?>
          <p class="mut">Se abre el <?= h(fechaCorta(fechaDelDia($consulta['fecha_inicio'], $dia))) ?>.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php if ($hayButeyko): ?><p class="mut"><?= h(AVISO_RESPIRACION) ?></p><?php endif; ?>

<h2 style="margin-top:32px">Talleres para seguir</h2>
<?php
$tw = $pdo->prepare("SELECT taller, estado FROM consultante_talleres WHERE usuario_id = ?");
$tw->execute([$usuario['id']]);
$misTalleres = [];
foreach ($tw->fetchAll(PDO::FETCH_ASSOC) as $f) $misTalleres[$f['taller']] = $f['estado'];

$orden = array_merge(talleresDeConsulta($consulta['talleres_recomendados']), array_keys($catalogo));
$orden = array_values(array_unique($orden));
foreach ($orden as $clave):
  $t = $catalogo[$clave];
  $estado = $misTalleres[$clave] ?? null;
  $recomendado = in_array($clave, talleresDeConsulta($consulta['talleres_recomendados']), true);
  $wa = 'https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, quiero información para inscribirme al taller ' . $t['nombre'] . '.');
?>
  <div class="card" style="margin:12px 0">
    <p class="fila" style="margin:0 0 6px">
      <?= $recomendado ? '<span class="chip" style="background:#F5C343">Recomendado para ti</span>' : '' ?>
      <?php if ($estado): ?><span class="chip" data-estado="<?= h($clave) ?>"><?= h($estados[$estado]) ?></span><?php endif; ?>
    </p>
    <h3 style="margin:4px 0"><?= h($t['nombre']) ?></h3>
    <p><?= h($t['resumen']) ?></p>
    <p class="mut">Base: <?= h(implode(' · ', array_map(fn($b) => $bases[$b], $t['bases']))) ?></p>
    <div class="fila">
      <a class="btn btn-outline-dark" href="<?= h($t['pagina']) ?>">Conocer el taller</a>
      <?php if (!in_array($estado, ['inscrito', 'completado'], true)): ?>
        <button type="button" class="btn btn-outline-dark" data-interes="<?= h($clave) ?>">Me interesa</button>
        <a class="btn btn-primary" target="_blank" rel="noopener" href="<?= h($wa) ?>">Inscribirme por WhatsApp</a>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<p class="mut" style="margin-top:28px">Este acompañamiento es educativo y complementario; no reemplaza atención médica ni psicológica. Si algo te desborda, escríbeme por WhatsApp.</p>

<script>
(function () {
  var csrf = <?= json_encode(tokenCsrf()) ?>;
  var consultaId = <?= (int)$consulta['id'] ?>;
  function llamar(cuerpo) {
    return fetch('/camino-accion.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF': csrf },
      body: JSON.stringify(cuerpo)
    }).then(function (r) { return r.json().then(function (d) { if (!r.ok) throw new Error(d.error || 'Error'); return d; }); });
  }
  document.querySelectorAll('input[data-ej]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      var marcado = cb.checked;
      llamar({ accion: 'completar', consulta_id: consultaId, ejercicio_id: parseInt(cb.dataset.ej, 10), hecho: marcado })
        .then(function (d) {
          document.getElementById('conteo').textContent = d.hechos + ' de ' + d.total;
          document.getElementById('barra').style.width = (d.total ? Math.round(d.hechos * 100 / d.total) : 0) + '%';
        })
        .catch(function (e) { cb.checked = !marcado; alert(e.message); });
    });
  });
  document.querySelectorAll('button[data-interes]').forEach(function (b) {
    b.addEventListener('click', function () {
      llamar({ accion: 'interes', taller: b.dataset.interes }).then(function () {
        b.textContent = '¡Anotado!'; b.disabled = true;
      }).catch(function (e) { alert(e.message); });
    });
  });
})();
</script>
<?php paginaFin();
