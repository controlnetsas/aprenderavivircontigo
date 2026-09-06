// Aprender a Vivir Contigo — Motor genérico de test paso a paso (una pregunta a la vez, en
// orden aleatorio, con barra de progreso) para el Test del Eneatipo y el Test de las Heridas de
// la Infancia. Al final cuenta cuántas veces respondiste "Sí" en cada categoría y muestra cuál
// predomina, con su descripción completa (la misma que aparece en reconocete-modulo-3.html).
//
// Sigue siendo una guía de autoobservación, no una prueba psicométrica clínicamente validada —
// el conteo de respuestas es orientativo, para ayudarte a reconocerte, no un diagnóstico.
//
// Depende de reconocete.js (window.RECONOCETE_ENEATIPOS / RECONOCETE_HERIDAS, las descripciones
// completas) y reconocete-test.js (window.RECONOCETE_TEST_ENEATIPOS / RECONOCETE_TEST_HERIDAS,
// de donde se toma el grupo "Reconocer el patrón hoy" como banco de preguntas del test rápido).

(function () {
  'use strict';

  function barajar(arr) {
    var copia = arr.slice();
    for (var i = copia.length - 1; i > 0; i--) {
      var j = Math.floor(Math.random() * (i + 1));
      var tmp = copia[i]; copia[i] = copia[j]; copia[j] = tmp;
    }
    return copia;
  }

  // ---- Paleta de colores por categoría (sin colores al azar: se interpolan tonos de la propia
  // marca, como los 9 puntos de un eneagrama recorriendo una rueda de color azul→rosa→dorado, y
  // las 5 heridas recorriendo rosa claro→ciruela→azul noche, para distinguirlas de un vistazo). ----
  function hexARgb(hex) {
    hex = hex.replace('#', '');
    return [parseInt(hex.substr(0, 2), 16), parseInt(hex.substr(2, 2), 16), parseInt(hex.substr(4, 2), 16)];
  }
  function rgbAHex(rgb) {
    return '#' + rgb.map(function (v) {
      var n = Math.round(Math.max(0, Math.min(255, v)));
      var s = n.toString(16);
      return s.length === 1 ? '0' + s : s;
    }).join('');
  }
  function mezclarColor(hexA, hexB, t) {
    var a = hexARgb(hexA), b = hexARgb(hexB);
    return rgbAHex([a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, a[2] + (b[2] - a[2]) * t]);
  }
  function colorEnEscala(paradas, t) {
    var n = paradas.length - 1;
    var pos = Math.max(0, Math.min(1, t)) * n;
    var i = Math.min(n - 1, Math.floor(pos));
    return mezclarColor(paradas[i], paradas[i + 1], pos - i);
  }
  var ESCALA_ENEATIPO = ['#211A33', '#C2607D', '#E0A92E'];
  var ESCALA_HERIDA = ['#E08FA0', '#7A4C6E', '#211A33'];
  var ESCALA_TEMA = ['#E0A92E', '#C2607D', '#2c2344'];
  var ESCALA_VIENTRE = ['#C2607D', '#211A33'];
  function colorPara(tipo, idx, total) {
    var escala = tipo === 'eneatipo' ? ESCALA_ENEATIPO : (tipo === 'herida' ? ESCALA_HERIDA : (tipo === 'vientre' ? ESCALA_VIENTRE : ESCALA_TEMA));
    return colorEnEscala(escala, total > 1 ? idx / (total - 1) : 0);
  }

  function construirPool(temasTest, prefijo) {
    var pool = [];
    temasTest.forEach(function (tema, catIdx) {
      var grupoDirecto = tema.grupos[0];
      grupoDirecto.preguntas.forEach(function (p, i) {
        pool.push({ id: prefijo + '-' + catIdx + '-' + i, catIdx: catIdx, pregunta: p });
      });
    });
    return pool;
  }

  function perfilHtml(perfil, tipo, color) {
    if (tipo === 'vientre') {
      return (
        '<div class="reco-resultado-perfil" style="--cat-color:' + color + ';">' +
          '<div class="reco-resultado-titulo-fila"><span class="reco-resultado-swatch"></span><h3 class="mt-0" style="margin:0;">' + perfil.nombre + '</h3></div>' +
          '<p style="font-style:italic;color:var(--rosa-vinculo);margin-bottom:16px;">' + perfil.frase + '</p>' +
          '<p><strong>Cómo se manifiesta hoy:</strong> ' + perfil.manifestacion + '</p>' +
          '<p style="margin-top:12px;"><strong>De dónde puede venir:</strong> ' + perfil.origen + '</p>' +
          '<p style="margin-top:12px;"><strong>Invitación:</strong> ' + perfil.invitacion + '</p>' +
        '</div>'
      );
    }
    var nombreMostrado = tipo === 'eneatipo' ? ('Eneatipo ' + perfil.n + ' · ' + perfil.nombre) : (perfil.nombre + ' · Máscara ' + perfil.mascara);
    return (
      '<div class="reco-resultado-perfil" style="--cat-color:' + color + ';">' +
        '<div class="reco-resultado-titulo-fila"><span class="reco-resultado-swatch"></span><h3 class="mt-0" style="margin:0;">' + nombreMostrado + '</h3></div>' +
        '<p style="font-style:italic;color:var(--rosa-vinculo);margin-bottom:16px;">Antídoto: ' + perfil.antidoto + '</p>' +
        '<p><strong>En resumen:</strong> ' + perfil.resumen + '</p>' +
        '<p style="margin-top:12px;"><strong>En la infancia:</strong> ' + perfil.infancia + '</p>' +
        '<p style="margin-top:12px;"><strong>En la juventud:</strong> ' + perfil.juventud + '</p>' +
        '<p style="margin-top:12px;"><strong>En la adultez:</strong> ' + perfil.adultez + '</p>' +
        '<p style="margin-top:12px;"><strong>Por qué se construyó:</strong> ' + perfil.porque + '</p>' +
        '<p style="margin-top:12px;"><strong>Ejercicio de resignificación:</strong> ' + perfil.ejercicio + '</p>' +
      '</div>'
    );
  }

  function crearQuiz(config) {
    var cont = document.getElementById(config.contenedorId);
    if (!cont) return null;

    var pool = barajar(construirPool(config.temasTest, config.prefijo));
    var respuestas = {}; // id -> 'Sí' | 'No' | 'No me identifico'
    var indice = 0;

    function render() {
      if (indice >= pool.length) { renderResultado(); return; }
      var item = pool[indice];
      var elegido = respuestas[item.id];
      var pct = Math.round((indice / pool.length) * 100);

      cont.innerHTML =
        '<div class="reco-quiz-progreso">' +
          '<div class="reco-quiz-barra-fondo"><div class="reco-quiz-barra" style="width:' + pct + '%;"></div></div>' +
          '<span>Pregunta ' + (indice + 1) + ' de ' + pool.length + '</span>' +
        '</div>' +
        '<p class="reco-quiz-progreso-nota">Las preguntas van cambiando de tema al azar en cada paso, así que no hay un orden que puedas anticipar.</p>' +
        '<div class="reco-quiz-pregunta">' +
          '<span class="reco-quiz-numero">' + (indice + 1) + '</span>' +
          '<p>' + item.pregunta + '</p>' +
          '<div class="reco-opciones" role="radiogroup">' +
            ['Sí', 'No', 'No me identifico'].map(function (op) {
              var activa = elegido === op ? ' reco-opcion-activa' : '';
              return '<button type="button" class="reco-quiz-btn' + activa + '" data-valor="' + op + '">' + op + '</button>';
            }).join('') +
          '</div>' +
        '</div>' +
        '<div class="reco-quiz-nav">' +
          '<button type="button" class="btn btn-outline-dark" id="reco-quiz-atras"' + (indice === 0 ? ' disabled' : '') + '>Atrás</button>' +
        '</div>';

      cont.querySelectorAll('.reco-quiz-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          respuestas[item.id] = btn.getAttribute('data-valor');
          indice++;
          render();
        });
      });
      var btnAtras = document.getElementById('reco-quiz-atras');
      if (btnAtras) btnAtras.addEventListener('click', function () { if (indice > 0) { indice--; render(); } });
    }

    function calcularConteo() {
      var conteo = config.categorias.map(function () { return 0; });
      var totalPorCategoria = config.categorias.map(function () { return 0; });
      pool.forEach(function (item) {
        totalPorCategoria[item.catIdx]++;
        if (respuestas[item.id] === 'Sí') conteo[item.catIdx]++;
      });
      return { conteo: conteo, total: totalPorCategoria };
    }

    function donutHtml(conteo) {
      var total = conteo.reduce(function (a, b) { return a + b; }, 0);
      if (!total) return '';
      var acc = 0;
      var partes = [];
      var leyenda = [];
      config.categorias.forEach(function (cat, i) {
        if (!conteo[i]) return;
        var color = colorPara(config.tipo, i, config.categorias.length);
        var desde = (acc / total) * 360;
        acc += conteo[i];
        var hasta = (acc / total) * 360;
        partes.push(color + ' ' + desde.toFixed(1) + 'deg ' + hasta.toFixed(1) + 'deg');
        leyenda.push(
          '<li><span class="reco-resultado-punto" style="--dot-color:' + color + ';"></span>' +
          config.nombreCategoria(cat) + ' — ' + conteo[i] + ' Sí</li>'
        );
      });
      return (
        '<div class="reco-donut-wrap">' +
          '<div class="reco-donut" style="background:conic-gradient(' + partes.join(', ') + ');" aria-hidden="true"></div>' +
          '<ul class="reco-donut-legend">' + leyenda.join('') + '</ul>' +
        '</div>'
      );
    }

    function recomendacionHonraVientreHtml() {
      return (
        '<div class="reco-resultado-desglose">' +
          '<h4>Una invitación a resignificar la honra a tus padres</h4>' +
          '<p>Honrar a tu padre y a tu madre no significa aprobar lo que hicieron, ni negar el dolor que haya quedado de esa relación. Honrar, en el sentido sistémico, es reconocer simplemente que a través de ellos recibiste la vida — con todo lo que pudieron dar y con todo lo que no. Puedes tomar la vida que te dieron y dejar en ellos lo que les pertenece: sus propias heridas, sus decisiones y su historia.</p>' +
          '<p style="margin-top:14px;"><strong>Algunas formas de empezar a resignificar este vínculo:</strong></p>' +
          '<ul class="lista-simple" style="margin-top:8px;">' +
            '<li>Separa a la persona de sus actos: puedes reconocer el daño sin seguir cargándolo como si fuera tuyo.</li>' +
            '<li>Prueba una frase sencilla, en silencio o en voz alta: "Te doy el lugar de padre/madre, y tomo mi lugar de hijo/hija."</li>' +
            '<li>Date permiso de sentir lo que aparezca —rabia, tristeza, distancia— sin apurarte a "perdonar" antes de tiempo.</li>' +
          '</ul>' +
          '<p style="margin-top:14px;">Esto no se resuelve solo con leerlo: el Taller Regresando al Vientre es justamente el espacio para trabajar este reconocimiento con acompañamiento.</p>' +
        '</div>'
      );
    }

    function ctaTallerHtml(top) {
      if (config.tipo === 'vientre') {
        var nombresVientre = top.map(function (t) { return t.cat.nombre; }).join(' y ');
        var textoWspVientre = encodeURIComponent(
          'Hola, hice el test Regresando al Vientre y me reconocí en: ' + (nombresVientre || 'un patrón') +
          '. Quiero información sobre el Taller Regresando al Vientre.'
        );
        return (
          '<div class="reco-cta-taller">' +
            '<h4>Una invitación, no una etiqueta</h4>' +
            '<p>Esto no es un diagnóstico ni una sentencia — es una invitación a reconocer, con honestidad y sin culpa, un conflicto inconsciente que probablemente lleva tiempo influyendo en tus relaciones, tus decisiones y tu forma de recibir la vida. El primer paso para empezar a sanarlo es simplemente nombrarlo. El Taller Regresando al Vientre es un espacio guiado para hacer justamente eso, con acompañamiento.</p>' +
            '<div class="reco-cta-botones">' +
              '<a href="regresando-al-vientre.html#top" class="btn btn-primary">Conocer el Taller Regresando al Vientre</a>' +
              '<a href="https://wa.me/573176435131?text=' + textoWspVientre + '" target="_blank" rel="noopener" class="btn btn-outline-dark">Preguntar por WhatsApp</a>' +
            '</div>' +
          '</div>'
        );
      }
      var nombres = top.map(function (t) {
        return config.tipo === 'eneatipo'
          ? ('Eneatipo ' + t.cat.n + ' (' + t.cat.nombre + ')')
          : (t.cat.nombre + ' (Máscara ' + t.cat.mascara + ')');
      }).join(' y ');
      var explicacion = config.tipo === 'eneatipo'
        ? 'Ya viste el patrón que más se repite en tus respuestas. El siguiente paso es reconocer cómo ese Eneatipo rige tu personalidad día a día —tus reacciones automáticas, tus miedos y tus fortalezas— y empezar a soltar lo que ya no te sirve.'
        : 'Ya viste cuál Herida de la Infancia aparece con más fuerza en tus respuestas. El siguiente paso es aprender a reconocerla cuando se activa, y comenzar los ejercicios de resignificación para empezar a sanarla, con acompañamiento.';
      var textoWsp = encodeURIComponent(
        'Hola, hice el test de Reconóce TE y me identifiqué con ' + (nombres || 'un patrón') +
        '. Quiero información sobre el Taller Reconócete — Módulo III.'
      );
      return (
        '<div class="reco-cta-taller">' +
          '<h4>Tu siguiente paso: Taller Reconócete — Módulo III</h4>' +
          '<p>' + explicacion + '</p>' +
          '<div class="reco-cta-botones">' +
            '<a href="reconocete-modulo-3.html" class="btn btn-primary">Ver el Módulo III completo</a>' +
            '<a href="https://wa.me/573176435131?text=' + textoWsp + '" target="_blank" rel="noopener" class="btn btn-outline-dark">Preguntar por WhatsApp</a>' +
          '</div>' +
        '</div>'
      );
    }

    function renderResultado() {
      var r = calcularConteo();
      var maxConteo = Math.max.apply(null, r.conteo);
      var top = config.categorias
        .map(function (cat, i) { return { cat: cat, i: i, n: r.conteo[i], total: r.total[i] }; })
        .filter(function (c) { return c.n === maxConteo && maxConteo > 0; });

      var barras = config.categorias.map(function (cat, i) {
        var pct = r.total[i] ? Math.round((r.conteo[i] / r.total[i]) * 100) : 0;
        var nombreCat = config.nombreCategoria(cat);
        var color = colorPara(config.tipo, i, config.categorias.length);
        return (
          '<div class="reco-resultado-fila">' +
            '<span class="reco-resultado-nombre"><span class="reco-resultado-punto" style="--dot-color:' + color + ';"></span>' + nombreCat + '</span>' +
            '<div class="reco-quiz-barra-fondo"><div class="reco-quiz-barra" style="width:' + pct + '%;background:' + color + ';"></div></div>' +
            '<span class="reco-resultado-conteo">' + r.conteo[i] + '/' + r.total[i] + '</span>' +
          '</div>'
        );
      }).join('');

      var perfiles = top.length
        ? top.map(function (t) { return perfilHtml(t.cat, config.tipo, colorPara(config.tipo, t.i, config.categorias.length)); }).join('')
        : '<p>No se marcó ningún "Sí" — vuelve a hacer el test respondiendo con lo primero que sientas, sin pensarlo demasiado.</p>';

      var desglose = config.tipo === 'vientre'
        ? recomendacionHonraVientreHtml()
        : '<div class="reco-resultado-desglose"><h4>Desglose de todas tus respuestas</h4>' + donutHtml(r.conteo) + barras + '</div>';

      cont.innerHTML =
        '<div class="reco-resultado">' +
          '<span class="small-caps eyebrow-gap">Resultado orientativo</span>' +
          '<h3 class="mt-0">' + (top.length > 1 ? 'Predominan varios patrones en tus respuestas' : (top.length === 1 ? 'El patrón que más aparece en tus respuestas es:' : 'Aún no hay un patrón claro')) + '</h3>' +
          perfiles +
          desglose +
          '<p class="genea-nota">Esto es una guía de autoobservación, no un diagnóstico clínico ni una prueba psicométrica validada — es normal reconocerte en más de un patrón.</p>' +
          (top.length ? ctaTallerHtml(top) : '') +
          '<button type="button" class="btn btn-outline-dark" id="reco-quiz-reiniciar">Volver a hacer el test</button>' +
        '</div>';

      var btnReiniciar = document.getElementById('reco-quiz-reiniciar');
      if (btnReiniciar) btnReiniciar.addEventListener('click', function () {
        respuestas = {};
        indice = 0;
        pool = barajar(construirPool(config.temasTest, config.prefijo));
        render();
      });

      if (config.onFinish) config.onFinish(r, top);
    }

    render();
    return { obtenerRespuestas: function () { return respuestas; }, obtenerPool: function () { return pool; } };
  }

  window.AVC_RECO_QUIZ = { crearQuiz: crearQuiz, colorPara: colorPara };

  window.AVC_RECO_QUIZ.instancias = {};

  document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('quiz-eneatipos') && window.RECONOCETE_TEST_ENEATIPOS && window.RECONOCETE_ENEATIPOS) {
      window.AVC_RECO_QUIZ.instancias.eneatipos = crearQuiz({
        contenedorId: 'quiz-eneatipos',
        prefijo: 'en',
        tipo: 'eneatipo',
        temasTest: window.RECONOCETE_TEST_ENEATIPOS,
        categorias: window.RECONOCETE_ENEATIPOS,
        nombreCategoria: function (cat) { return 'Eneatipo ' + cat.n + ' · ' + cat.nombre; }
      });
    }
    if (document.getElementById('quiz-heridas') && window.RECONOCETE_TEST_HERIDAS && window.RECONOCETE_HERIDAS) {
      window.AVC_RECO_QUIZ.instancias.heridas = crearQuiz({
        contenedorId: 'quiz-heridas',
        prefijo: 'he',
        tipo: 'herida',
        temasTest: window.RECONOCETE_TEST_HERIDAS,
        categorias: window.RECONOCETE_HERIDAS,
        nombreCategoria: function (cat) { return cat.nombre + ' · Máscara ' + cat.mascara; }
      });
    }
    if (document.getElementById('quiz-vientre') && window.VIENTRE_TEST_PREGUNTAS && window.VIENTRE_PERFILES) {
      window.AVC_RECO_QUIZ.instancias.vientre = crearQuiz({
        contenedorId: 'quiz-vientre',
        prefijo: 'vi',
        tipo: 'vientre',
        temasTest: window.VIENTRE_TEST_PREGUNTAS,
        categorias: window.VIENTRE_PERFILES,
        nombreCategoria: function (cat) { return cat.nombre; }
      });
    }
  });
})();
