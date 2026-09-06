// Aprender a Vivir Contigo — Genera y descarga el reporte HTML del Test Regresando al Vientre
// (vínculo con la madre y con el padre). Depende de reco-quiz.js (window.AVC_RECO_QUIZ.instancias.vientre).

(function () {
  'use strict';

  function escapeHtml(texto) {
    return String(texto)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function calcularResultado(instancia, categorias, nombreCategoria) {
    if (!instancia) return null;
    var pool = instancia.obtenerPool();
    var respuestas = instancia.obtenerRespuestas();
    var conteo = categorias.map(function () { return 0; });
    var total = categorias.map(function () { return 0; });
    pool.forEach(function (item) {
      total[item.catIdx]++;
      if (respuestas[item.id] === 'Sí') conteo[item.catIdx]++;
    });
    var maxConteo = Math.max.apply(null, conteo);
    var top = categorias
      .map(function (cat, i) { return { cat: cat, i: i, n: conteo[i], total: total[i] }; })
      .filter(function (c) { return c.n === maxConteo && maxConteo > 0; });

    var filasQyA = pool.map(function (item) {
      return { pregunta: item.pregunta, respuesta: respuestas[item.id] || '(sin responder aún)' };
    });

    return { conteo: conteo, total: total, top: top, filasQyA: filasQyA, nombreCategoria: nombreCategoria, categorias: categorias };
  }

  function colorPara(idx, total) {
    return (window.AVC_RECO_QUIZ && window.AVC_RECO_QUIZ.colorPara)
      ? window.AVC_RECO_QUIZ.colorPara('vientre', idx, total)
      : '#C2607D';
  }

  function perfilBloque(perfil, color) {
    return (
      '<div class="perfil" style="border-left:5px solid ' + color + ';">' +
        '<h3><span class="swatch" style="background:' + color + ';"></span>' + escapeHtml(perfil.nombre) + '</h3>' +
        '<p class="antidoto">' + escapeHtml(perfil.frase) + '</p>' +
        '<p><strong>Cómo se manifiesta hoy:</strong> ' + escapeHtml(perfil.manifestacion) + '</p>' +
        '<p><strong>De dónde puede venir:</strong> ' + escapeHtml(perfil.origen) + '</p>' +
        '<p><strong>Invitación:</strong> ' + escapeHtml(perfil.invitacion) + '</p>' +
      '</div>'
    );
  }

  function donutBloque(resultado) {
    var total = resultado.conteo.reduce(function (a, b) { return a + b; }, 0);
    if (!total) return '';
    var acc = 0;
    var partes = [];
    var leyenda = [];
    resultado.categorias.forEach(function (cat, i) {
      if (!resultado.conteo[i]) return;
      var color = colorPara(i, resultado.categorias.length);
      var desde = (acc / total) * 360;
      acc += resultado.conteo[i];
      var hasta = (acc / total) * 360;
      partes.push(color + ' ' + desde.toFixed(1) + 'deg ' + hasta.toFixed(1) + 'deg');
      leyenda.push('<li><span class="punto" style="background:' + color + ';"></span>' + escapeHtml(resultado.nombreCategoria(cat)) + ' — ' + resultado.conteo[i] + ' Sí</li>');
    });
    return (
      '<div class="donut-wrap">' +
        '<div class="donut" style="background:conic-gradient(' + partes.join(', ') + ');"></div>' +
        '<ul class="leyenda">' + leyenda.join('') + '</ul>' +
      '</div>'
    );
  }

  function ctaTallerBloque(resultado) {
    if (!resultado.top.length) return '';
    var nombres = resultado.top.map(function (t) { return t.cat.nombre; }).join(' y ');
    var textoWsp = encodeURIComponent('Hola, hice el test Regresando al Vientre y me reconocí en: ' + nombres + '. Quiero información sobre el Taller Regresando al Vientre.');
    return (
      '<div class="cta-taller">' +
        '<h4>Una invitación, no una etiqueta</h4>' +
        '<p>Esto no es un diagnóstico ni una sentencia — es una invitación a reconocer, con honestidad y sin culpa, un conflicto inconsciente que probablemente lleva tiempo influyendo en tus relaciones, tus decisiones y tu forma de recibir la vida. El primer paso para empezar a sanarlo es simplemente nombrarlo.</p>' +
        '<p>' +
          '<a href="https://www.aprenderavivircontigo.com/regresando-al-vientre.html" target="_blank" rel="noopener">Conocer el Taller Regresando al Vientre</a>' +
          ' &nbsp;·&nbsp; ' +
          '<a href="https://wa.me/573176435131?text=' + textoWsp + '" target="_blank" rel="noopener">Preguntar por WhatsApp</a>' +
        '</p>' +
      '</div>'
    );
  }

  function construirReporteHtml() {
    var resultado = calcularResultado(
      window.AVC_RECO_QUIZ.instancias && window.AVC_RECO_QUIZ.instancias.vientre,
      window.VIENTRE_PERFILES,
      function (cat) { return cat.nombre; }
    );
    if (!resultado) return null;

    var perfiles = resultado.top.length
      ? resultado.top.map(function (t) { return perfilBloque(t.cat, colorPara(t.i, resultado.categorias.length)); }).join('')
      : '<p><em>No se marcó ningún "Sí" en este test.</em></p>';

    var desglose = resultado.categorias.map(function (cat, i) {
      var pct = resultado.total[i] ? Math.round((resultado.conteo[i] / resultado.total[i]) * 100) : 0;
      var color = colorPara(i, resultado.categorias.length);
      return (
        '<tr><td><span class="punto" style="background:' + color + ';"></span>' + escapeHtml(resultado.nombreCategoria(cat)) + '</td>' +
        '<td>' + resultado.conteo[i] + '/' + resultado.total[i] + ' (' + pct + '%)</td>' +
        '<td><div class="barra-fondo"><div class="barra" style="width:' + pct + '%;background:' + color + ';"></div></div></td></tr>'
      );
    }).join('');

    var preguntas = resultado.filasQyA.map(function (f) {
      return '<tr><td>' + escapeHtml(f.pregunta) + '</td><td>' + escapeHtml(f.respuesta) + '</td></tr>';
    }).join('');

    var fecha = new Date().toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });

    return (
      '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">' +
      '<title>Mi resultado — Regresando al Vientre</title>' +
      '<style>' +
        'body{font-family:Georgia,serif;max-width:760px;margin:40px auto;padding:0 20px;color:#211a33;line-height:1.6;}' +
        'h1{font-family:Arial,sans-serif;color:#211a33;} h2{font-family:Arial,sans-serif;color:#211a33;border-top:3px solid #f5c343;padding-top:16px;margin-top:40px;}' +
        'h3{font-family:Arial,sans-serif;color:#211a33;margin-bottom:4px;} h4{font-family:Arial,sans-serif;color:#211a33;}' +
        '.antidoto{font-style:italic;color:#c76b8a;margin-top:0;}' +
        '.nota{font-size:0.85rem;color:#c76b8a;background:#fdf6ea;padding:10px 14px;border-radius:8px;}' +
        '.perfil{background:#fdf6ea;border-radius:10px;padding:18px 22px;margin-bottom:18px;}' +
        '.swatch{display:inline-block;width:14px;height:14px;border-radius:50%;margin-right:8px;vertical-align:middle;}' +
        'table{border-collapse:collapse;width:100%;margin-bottom:18px;}' +
        '.tabla-desglose td{padding:6px 8px;border-bottom:1px solid #eee;font-size:0.9rem;}' +
        '.barra-fondo{background:#eee;border-radius:999px;height:10px;overflow:hidden;}' +
        '.barra{height:100%;}' +
        '.punto{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:7px;}' +
        '.donut-wrap{display:flex;align-items:center;gap:24px;flex-wrap:wrap;margin:14px 0 22px;}' +
        '.donut{width:120px;height:120px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 5px #fff, 0 1px 6px rgba(33,26,51,0.15);}' +
        '.leyenda{list-style:none;padding:0;margin:0;font-size:0.85rem;}' +
        '.leyenda li{margin-bottom:6px;}' +
        '.cta-taller{margin-top:18px;padding:18px 20px;border-radius:10px;background:linear-gradient(135deg,rgba(245,195,67,0.16),rgba(194,96,125,0.1));border:1px solid rgba(224,169,46,0.4);}' +
        '.cta-taller h4{margin-top:0;}' +
        '.cta-taller a{color:#a3315a;font-weight:bold;}' +
        '.tabla-qya th,.tabla-qya td{padding:6px 8px;border-bottom:1px solid #eee;font-size:0.85rem;text-align:left;}' +
        'details summary{cursor:pointer;color:#c76b8a;margin-bottom:10px;}' +
        'footer{margin-top:50px;font-size:0.8rem;color:#888;border-top:1px solid #ddd;padding-top:14px;}' +
      '</style></head><body>' +
      '<h1>Mi resultado — Regresando al Vientre</h1>' +
      '<p>Aprender a Vivir Contigo · aprenderavivircontigo.com<br>Descargado: ' + fecha + '</p>' +
      '<p class="nota">Este documento es privado y se generó solo en tu navegador — nunca se envió a ningún servidor. Es una guía de autoobservación, no un diagnóstico clínico ni una prueba psicométrica validada. Llévalo al Taller Regresando al Vientre si quieres profundizar en lo que apareció.</p>' +
      '<h2>Vínculo con la Madre y con el Padre</h2>' +
      perfiles +
      '<h4>Desglose por categoría</h4>' +
      donutBloque(resultado) +
      '<table class="tabla-desglose">' + desglose + '</table>' +
      '<details><summary>Ver todas las preguntas y respuestas</summary>' +
        '<table class="tabla-qya"><tr><th>Pregunta</th><th>Respuesta</th></tr>' + preguntas + '</table>' +
      '</details>' +
      ctaTallerBloque(resultado) +
      '<footer>Aprender a Vivir Contigo — Reconocer para reconciliar. www.aprenderavivircontigo.com</footer>' +
      '</body></html>'
    );
  }

  function descargar() {
    var html = construirReporteHtml();
    if (!html) return;
    var blob = new Blob([html], { type: 'text/html;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'mi-resultado-regresando-al-vientre.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(function () { URL.revokeObjectURL(url); }, 500);
  }

  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btn-descargar-vientre');
    if (btn) btn.addEventListener('click', descargar);
  });
})();
