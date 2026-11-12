<?php
// informe-evaluacion.php — arma el informe (HTML) de una evaluación inicial a partir de la ficha y los tests.
require_once __DIR__ . '/ficha-campos.php';

function ultimoResultado($pdo, $usuarioId, $test, $parte = null) {
  $s = $pdo->prepare("SELECT resumen, datos, creado_en FROM resultados_tests WHERE usuario_id = ? AND test = ? ORDER BY id DESC LIMIT 20");
  $s->execute([$usuarioId, $test]);
  foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $d = json_decode((string)$r['datos'], true) ?: [];
    if ($parte === null || ($d['parte'] ?? '') === $parte) return ['resumen' => $r['resumen'], 'datos' => $d, 'fecha' => $r['creado_en']];
  }
  return null;
}

function barrasInforme($categorias) {
  $out = '';
  foreach ($categorias as $c) {
    $pct = !empty($c['total']) ? (int)round(100 * $c['si'] / $c['total']) : 0;
    $out .= '<div style="display:flex;align-items:center;gap:10px;margin:4px 0;font-size:14px"><span style="flex:0 0 46%">' . h($c['nombre']) . '</span>'
          . '<span style="flex:1;background:#eadfc9;border-radius:6px;height:10px;overflow:hidden"><i style="display:block;height:100%;width:' . $pct . '%;background:#C2607D"></i></span>'
          . '<span style="flex:0 0 52px;text-align:right">' . (int)$c['si'] . '/' . (int)$c['total'] . '</span></div>';
  }
  return $out;
}

// Devuelve ['html' => ..., 'destacados' => [líneas cortas sobre los tests, sin datos de salud]]
function construirInformeEvaluacion($pdo, $ev, $usuario) {
  $uid = (int)$usuario['id'];
  $gen = ultimoResultado($pdo, $uid, 'genealogia');
  $ene = ultimoResultado($pdo, $uid, 'reconocete', 'eneatipo');
  $her = ultimoResultado($pdo, $uid, 'reconocete', 'herida');
  $vie = ultimoResultado($pdo, $uid, 'vientre');
  $partes = partesDeEvaluacion($ev);
  $destacados = [];
  $dificultades = [];

  $opC = opcionesConflictoFicha(); $opH = opcionesHeridasFicha();
  $conf = array_filter(explode(',', (string)$ev['conflictos_emocionales']));
  $herMarc = array_filter(explode(',', (string)$ev['heridas_infancia']));
  $det = json_decode((string)$ev['conflicto_descripcion'], true) ?: [];

  $html = '<div style="font-family:inherit">';
  $html .= '<h2 style="margin-top:0">Informe de evaluación inicial</h2>';
  $html .= '<p class="mut">' . h($usuario['nombre']) . ' · ' . h($usuario['email']) . ' · Cel. ' . h($usuario['telefono'] ?? '') . ' · ' . h(date('d/m/Y H:i')) . '</p>';

  // Datos básicos
  $filas = [
    'Edad' => $ev['edad'], 'Sexo' => $ev['sexo'], 'Síntomas' => $ev['sintomas'], 'Situación sentimental' => $ev['situacion_sentimental'],
    'Hijos y nombres' => $ev['hijos'], 'Hijos no nacidos o muertos al nacer' => $ev['hijos_no_nacidos'], 'Estado de salud' => $ev['estado_salud'],
    'Trabajo actual' => $ev['trabajo_actual'], 'Profesión' => $ev['profesion'],
  ];
  $html .= '<h3>Datos básicos</h3><table class="tabla">';
  foreach ($filas as $k => $v) $html .= '<tr><th style="width:36%">' . h($k) . '</th><td>' . (($v === null || $v === '') ? '<span class="mut">—</span>' : nl2br(h($v))) . '</td></tr>';
  $html .= '</table>';

  // Conflictos
  $html .= '<h3>Conflictos emocionales que la persona siente sin resolver</h3>';
  if ($conf) {
    $html .= '<ul>';
    foreach ($conf as $k) {
      $dificultades[] = $opC[$k] ?? $k;
      $html .= '<li><strong>' . h($opC[$k] ?? $k) . '</strong>';
      if ($k === 'heridas_infancia' && $herMarc) $html .= ' — heridas marcadas: ' . h(implode(', ', array_map(fn($x) => $opH[$x] ?? $x, $herMarc)));
      if (!empty($det[$k])) $html .= '<br>' . nl2br(h($det[$k]));
      $html .= '</li>';
    }
    $html .= '</ul>';
  } else $html .= '<p class="mut">No marcó ninguno.</p>';

  // Eneatipo
  if (in_array('eneatipo', $partes, true)) { $html .= '<h3>Test de Eneatipos (Reconóce TE)</h3>';
  if ($ene) {
    $pred = $ene['datos']['predominan'] ?? [];
    $html .= '<p><strong>Predomina:</strong> ' . h($pred ? implode(' / ', $pred) : 'sin patrón claro') . '</p>' . barrasInforme($ene['datos']['categorias'] ?? []);
    $destacados[] = 'Eneatipo: ' . ($pred ? implode(' / ', $pred) : 'sin patrón claro');
    foreach ($pred as $p) $dificultades[] = $p;
  } else $html .= '<p class="mut">No realizado.</p>'; }

  // Heridas
  if (in_array('herida', $partes, true)) { $html .= '<h3>Test de Heridas de la Infancia (Reconóce TE)</h3>';
  if ($her) {
    $pred = $her['datos']['predominan'] ?? [];
    $html .= '<p><strong>Predomina:</strong> ' . h($pred ? implode(' / ', $pred) : 'sin patrón claro') . '</p>' . barrasInforme($her['datos']['categorias'] ?? []);
    $destacados[] = 'Heridas de la infancia: ' . ($pred ? implode(' / ', $pred) : 'sin patrón claro');
    foreach ($pred as $p) $dificultades[] = $p;
  } else $html .= '<p class="mut">No realizado.</p>'; }

  // Genealogía
  if (in_array('genealogia', $partes, true)) { $html .= '<h3>Test de Genealogía</h3>';
  if ($gen) {
    $temas = $gen['datos']['temas'] ?? [];
    $html .= barrasInforme($temas);
    $top = array_filter($temas, fn($t) => ($t['si'] ?? 0) > 0);
    usort($top, fn($a, $b) => $b['si'] <=> $a['si']);
    $top = array_slice($top, 0, 3);
    $destacados[] = 'Genealogía, más carga en: ' . ($top ? implode(', ', array_map(fn($t) => $t['nombre'] . ' (' . $t['si'] . ' Sí)', $top)) : 'sin respuestas "Sí"');
    foreach ($top as $t) $dificultades[] = $t['nombre'];
    $anc = $gen['datos']['ancestros'] ?? [];
    if ($anc) {
      $html .= '<h4>Árbol: ancestros que nombró</h4><ul>';
      foreach ($anc as $a) {
        $html .= '<li><strong>' . h($a['nombre'] ?? '?') . '</strong> (' . h($a['vinculo'] ?? '') . ')'
              . (!empty($a['memoria']) ? ' — ' . h($a['memoria']) : '')
              . (!empty($a['lealtad']) ? '<br><span class="mut">Lealtad invisible: ' . h($a['lealtad']) . '</span>' : '')
              . (!empty($a['lenguaje']) ? '<br><span class="mut">Lenguaje nuclear / patrón heredado: ' . h($a['lenguaje']) . '</span>' : '') . '</li>';
      }
      $html .= '</ul>';
    }
    $si = array_filter($gen['datos']['respuestas'] ?? [], fn($r) => ($r['respuesta'] ?? '') === 'Sí' || !empty($r['nota']));
    if ($si) {
      $html .= '<h4>Respuestas "Sí" y notas</h4><table class="tabla">';
      foreach ($si as $r) $html .= '<tr><td>' . h($r['tema'] ?? '') . '<br>' . h($r['pregunta'] ?? '') . '</td><td><strong>' . h($r['respuesta'] ?? '') . '</strong>' . (!empty($r['nota']) ? '<br>' . nl2br(h($r['nota'])) : '') . '</td></tr>';
      $html .= '</table>';
    }
  } else $html .= '<p class="mut">No realizado.</p>'; }

  // Regresando al Vientre
  if (in_array('vientre', $partes, true)) {
    $html .= '<h3>Test Regresando al Vientre</h3>';
    if ($vie) {
      $pred = $vie['datos']['predominan'] ?? [];
      $html .= '<p><strong>Mayor carga:</strong> ' . h($pred ? implode(' / ', $pred) : 'sin patrón claro') . '</p>' . barrasInforme($vie['datos']['categorias'] ?? []);
      $destacados[] = 'Regresando al Vientre: ' . ($pred ? implode(' / ', $pred) : 'sin patrón claro');
      foreach ($pred as $p) $dificultades[] = $p;
    } else $html .= '<p class="mut">No realizado.</p>';
  }

  // Síntesis
  $dificultades = array_values(array_unique($dificultades));
  $sintesis = '<h3>Posibles dificultades a explorar</h3>';
  $sintesis .= $dificultades ? '<ul><li>' . implode('</li><li>', array_map('h', $dificultades)) . '</li></ul>' : '<p class="mut">Sin elementos destacados.</p>';
  $html = str_replace('<h3>Datos básicos</h3>', $sintesis . '<h3>Datos básicos</h3>', $html);

  $html .= '<p class="mut" style="margin-top:24px">Informe orientativo, generado a partir de lo que la persona respondió. No es un diagnóstico clínico ni reemplaza atención médica o psicológica.</p></div>';
  return ['html' => $html, 'destacados' => $destacados];
}
