<?php
// ficha-campos.php — opciones y formulario de la ficha básica (evaluación inicial).
function opcionesHeridasFicha() {
  return ['abandono' => 'Abandono', 'rechazo' => 'Rechazo', 'humillacion' => 'Humillación', 'traicion' => 'Traición', 'injusticia' => 'Injusticia'];
}
function opcionesConflictoFicha() {
  return [
    'heridas_infancia' => 'Heridas de infancia', 'lealtad_ancestro' => 'Lealtad familiar con un ancestro',
    'conflicto_papa' => 'Conflicto con papá', 'conflicto_mama' => 'Conflicto con mamá', 'relaciones' => 'Relaciones',
    'salud' => 'Salud', 'trabajo' => 'Trabajo', 'salud_mental' => 'Salud mental',
  ];
}

// Lee y limpia la ficha enviada por POST. Devuelve arreglo listo para la base de datos.
function leerFichaPost() {
  $txt = function ($k, $max) { $v = trim((string)($_POST[$k] ?? '')); return $v === '' ? null : mb_substr($v, 0, $max); };
  $edad = ($_POST['edad'] ?? '') !== '' ? (int)$_POST['edad'] : null;
  if ($edad !== null && ($edad < 0 || $edad > 120)) $edad = null;
  $conf = array_values(array_intersect(array_keys(opcionesConflictoFicha()), (array)($_POST['conflictos'] ?? [])));
  $her = array_values(array_intersect(array_keys(opcionesHeridasFicha()), (array)($_POST['heridas'] ?? [])));
  $det = [];
  foreach ($conf as $k) { $t = trim((string)($_POST['desc'][$k] ?? '')); if ($t !== '') $det[$k] = mb_substr($t, 0, 2000); }
  return [
    'edad' => $edad,
    'sexo' => in_array($_POST['sexo'] ?? '', ['femenino', 'masculino', 'otro'], true) ? $_POST['sexo'] : null,
    'sintomas' => $txt('sintomas', 4000), 'situacion_sentimental' => $txt('situacion_sentimental', 190),
    'hijos' => $txt('hijos', 2000), 'hijos_no_nacidos' => $txt('hijos_no_nacidos', 2000),
    'estado_salud' => $txt('estado_salud', 4000), 'trabajo_actual' => $txt('trabajo_actual', 190), 'profesion' => $txt('profesion', 190),
    'conflictos_emocionales' => implode(',', $conf) ?: null,
    'conflicto_descripcion' => $det ? json_encode($det, JSON_UNESCAPED_UNICODE) : null,
    'heridas_infancia' => implode(',', $her) ?: null,
  ];
}

// Formulario con los valores actuales ($v = fila de evaluaciones o []).
function formularioFicha($v) {
  $sel = fn($campo) => array_filter(explode(',', (string)($v[$campo] ?? '')));
  $conSel = $sel('conflictos_emocionales'); $herSel = $sel('heridas_infancia');
  $det = json_decode((string)($v['conflicto_descripcion'] ?? ''), true) ?: [];
  $val = fn($k) => h($v[$k] ?? '');
  ob_start(); ?>
    <div class="fila">
      <div style="flex:1;min-width:120px"><label class="etq" for="edad">Edad</label>
        <input class="campo" id="edad" name="edad" type="number" min="0" max="120" inputmode="numeric" value="<?= $val('edad') ?>"></div>
      <div style="flex:2;min-width:160px"><label class="etq" for="sexo">Sexo</label>
        <select class="campo" id="sexo" name="sexo"><option value="">—</option>
          <?php foreach (['femenino' => 'Femenino', 'masculino' => 'Masculino', 'otro' => 'Otro'] as $k => $e): ?>
            <option value="<?= $k ?>" <?= ($v['sexo'] ?? '') === $k ? 'selected' : '' ?>><?= $e ?></option>
          <?php endforeach; ?></select></div>
    </div>
    <label class="etq" for="sintomas">Síntomas que te traen (físicos, emocionales)</label>
    <textarea class="campo" id="sintomas" name="sintomas" rows="3" maxlength="4000"><?= $val('sintomas') ?></textarea>
    <label class="etq" for="situacion_sentimental">Situación sentimental actual</label>
    <input class="campo" id="situacion_sentimental" name="situacion_sentimental" maxlength="190" value="<?= $val('situacion_sentimental') ?>" placeholder="Ej.: casada, separado hace 2 años, soltera…">
    <label class="etq" for="hijos">Hijos y sus nombres</label>
    <textarea class="campo" id="hijos" name="hijos" rows="2" maxlength="2000" placeholder="Ej.: Ana (12), Luis (8)"><?= $val('hijos') ?></textarea>
    <label class="etq" for="hijos_no_nacidos">Hijos no nacidos o muertos al nacer</label>
    <textarea class="campo" id="hijos_no_nacidos" name="hijos_no_nacidos" rows="2" maxlength="2000" placeholder="Abortos, pérdidas gestacionales, hijos fallecidos al nacer… (solo si quieres compartirlo)"><?= $val('hijos_no_nacidos') ?></textarea>
    <label class="etq" for="estado_salud">Estado de salud</label>
    <textarea class="campo" id="estado_salud" name="estado_salud" rows="3" maxlength="4000"><?= $val('estado_salud') ?></textarea>
    <div class="fila">
      <div style="flex:1;min-width:200px"><label class="etq" for="trabajo_actual">Trabajo actual</label>
        <input class="campo" id="trabajo_actual" name="trabajo_actual" maxlength="190" value="<?= $val('trabajo_actual') ?>"></div>
      <div style="flex:1;min-width:200px"><label class="etq" for="profesion">Profesión</label>
        <input class="campo" id="profesion" name="profesion" maxlength="190" value="<?= $val('profesion') ?>"></div>
    </div>
    <span class="etq" style="margin-top:22px">Conflicto emocional que sientes sin resolver</span>
    <p class="mut" style="margin-top:0">Marca los que apliquen; al marcar cada uno se abre su casilla para describirlo.</p>
    <?php foreach (opcionesConflictoFicha() as $clave => $etiqueta): ?>
      <div style="border:1px solid #eadfc9;border-radius:10px;padding:10px 14px;margin:8px 0;background:#fff">
        <label><input type="checkbox" name="conflictos[]" value="<?= h($clave) ?>" data-abre="det-<?= h($clave) ?>" <?= in_array($clave, $conSel, true) ? 'checked' : '' ?>> <strong><?= h($etiqueta) ?></strong></label>
        <div id="det-<?= h($clave) ?>" style="display:none;margin-top:8px">
          <?php if ($clave === 'heridas_infancia'): ?>
            <span class="mut">Herida(s) de infancia (puedes marcar varias):</span><br>
            <?php foreach (opcionesHeridasFicha() as $hk => $hEt): ?>
              <label style="display:inline-block;margin:4px 14px 4px 0"><input type="checkbox" name="heridas[]" value="<?= h($hk) ?>" <?= in_array($hk, $herSel, true) ? 'checked' : '' ?>> <?= h($hEt) ?></label>
            <?php endforeach; ?>
          <?php endif; ?>
          <textarea class="campo" name="desc[<?= h($clave) ?>]" rows="3" maxlength="2000" style="margin-top:8px"
            placeholder="Describe: <?= h(mb_strtolower($etiqueta)) ?> — qué lo dispara, desde cuándo, a quién involucra…"><?= h($det[$clave] ?? '') ?></textarea>
        </div>
      </div>
    <?php endforeach; ?>
    <script>
    document.querySelectorAll('input[data-abre]').forEach(function (cb) {
      function ver() { var el = document.getElementById(cb.dataset.abre); if (el) el.style.display = cb.checked ? 'block' : 'none'; }
      cb.addEventListener('change', ver); ver();
    });
    </script>
  <?php return ob_get_clean();
}
