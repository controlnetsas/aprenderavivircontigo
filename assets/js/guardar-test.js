// guardar-test.js — ofrece guardar el resultado de un test en el proceso de la persona
// (lo verá el terapeuta en la "ficha"). Solo se guarda si la persona pulsa el botón.
(function () {
  'use strict';
  var clave = window.AVC_TEST_CLAVE;
  var panel = document.getElementById('guardar-panel');
  if (!clave || !panel) return;

  function limpio(t) { return String(t || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim(); }
  function esc(t) { return String(t).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

  function mostrar(titulo, obtener) {
    panel.innerHTML =
      '<div class="card"><h3 class="mt-0">Guardar mi resultado</h3>' +
      '<p style="font-size:.92rem">' + esc(titulo) + ' Al guardarlo, Carlos (tu terapeuta) podrá verlo en tu ficha para acompañarte mejor. Si no quieres compartirlo, simplemente no lo guardes.</p>' +
      '<button type="button" class="btn btn-primary" id="btn-guardar-test">Guardar mi resultado</button> ' +
      '<span id="guardar-estado" class="mut" role="status"></span></div>';
    document.getElementById('btn-guardar-test').addEventListener('click', function () {
      var btn = this, est = document.getElementById('guardar-estado');
      var payload = obtener();
      btn.disabled = true; est.textContent = 'Guardando…';
      fetch('/guardar-test.php', {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF': window.AVC_CSRF },
        body: JSON.stringify({ test: clave, resumen: payload.resumen, datos: payload.datos })
      }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (x) {
          if (x.ok && x.d.ok) { est.textContent = '✓ Guardado. Gracias por compartirlo.'; }
          else { est.textContent = (x.d && x.d.error) || 'No se pudo guardar. Intenta de nuevo.'; btn.disabled = false; }
        })
        .catch(function () { est.textContent = 'Sin conexión. Intenta de nuevo.'; btn.disabled = false; });
    });
  }

  // ---- Reconóce TE y Regresando al Vientre: el motor de quizzes avisa al terminar ----
  var ROTULO = { eneatipo: 'Eneatipo', herida: 'Heridas de la infancia', vientre: 'Regresando al Vientre' };
  window.AVC_ON_TEST_FINISH = function (tipo, data) {
    var resumen = (ROTULO[tipo] || tipo) + ': ' + (data.predominan.length ? data.predominan.join(' / ') : 'sin patrón claro') +
      ' · ' + data.categorias.map(function (c) { return c.nombre + ' ' + c.si + '/' + c.total; }).join('; ');
    mostrar('Terminaste la parte "' + (ROTULO[tipo] || tipo) + '".', function () {
      return { resumen: resumen, datos: { parte: tipo, categorias: data.categorias, predominan: data.predominan } };
    });
  };

  // ---- Genealogía: se arma desde las respuestas marcadas en pantalla ----
  if (clave === 'genealogia') {
    function recolectar() {
      var temas = window.GENEALOGIA_TEST || [];
      var resumenTemas = [], respuestas = [];
      temas.forEach(function (tema, ti) {
        var si = 0, total = 0;
        tema.grupos.forEach(function (g, gi) {
          g.preguntas.forEach(function (p, pi) {
            var id = 'gp-' + ti + '-' + gi + '-' + pi;
            total++;
            var m = document.querySelector('input[name="' + id + '"]:checked');
            var n = document.getElementById(id + '-nota');
            var nota = n && n.value ? n.value.trim().slice(0, 500) : '';
            if (m && m.value === 'Sí') si++;
            if (m || nota) respuestas.push({ tema: limpio(tema.tema), pregunta: limpio(p).slice(0, 300), respuesta: m ? m.value : '', nota: nota });
          });
        });
        resumenTemas.push({ nombre: limpio(tema.tema), si: si, total: total });
      });
      var ancestros = (window.AVC_ARBOL && window.AVC_ARBOL.obtenerAncestros) ? window.AVC_ARBOL.obtenerAncestros() : [];
      var top = resumenTemas.filter(function (t) { return t.si > 0; }).sort(function (a, b) { return b.si - a.si; }).slice(0, 3);
      var resumen = 'Genealogía: ' + (top.length ? 'más carga en ' + top.map(function (t) { return t.nombre + ' (' + t.si + ' Sí)'; }).join(', ') : 'sin respuestas "Sí"') +
        ' · ' + ancestros.length + ' ancestros en el árbol';
      return { resumen: resumen, datos: { temas: resumenTemas, ancestros: ancestros, respuestas: respuestas } };
    }
    mostrar('Cuando termines de responder lo que quieras (puedes dejar preguntas sin responder),', recolectar);
  }
})();
