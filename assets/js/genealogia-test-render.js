// Aprender a Vivir Contigo — Renderiza el Test de Genealogía a partir de window.GENEALOGIA_TEST
// (ver genealogia-test.js). Es un cuestionario de autoindagación: las respuestas se escriben en
// el navegador, nunca se envían a ningún servidor, y al final se pueden descargar en un .txt.

(function () {
  'use strict';

  function slug(texto) {
    return texto.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita tildes
      .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  }

  var OPCIONES = ['Sí', 'No', 'No me identifico'];

  function preguntaId(temaIdx, grupoIdx, pIdx) {
    return 'gp-' + temaIdx + '-' + grupoIdx + '-' + pIdx;
  }

  function opcionesHtml(id) {
    var botones = OPCIONES.map(function (opcion, i) {
      var optId = id + '-op' + i;
      return (
        '<input type="radio" id="' + optId + '" name="' + id + '" value="' + escapeAttr(opcion) + '">' +
        '<label for="' + optId + '" data-valor="' + escapeAttr(opcion) + '">' + opcion + '</label>'
      );
    }).join('');
    return '<div class="reco-opciones" role="radiogroup">' + botones + '</div>';
  }

  function tarjetaTema(tema, temaIdx) {
    var grupos = tema.grupos.map(function (grupo, grupoIdx) {
      var nota = grupo.nota
        ? '<p class="genea-nota">' + grupo.nota + '</p>'
        : '';
      var preguntas = grupo.preguntas.map(function (p, pIdx) {
        var id = preguntaId(temaIdx, grupoIdx, pIdx);
        var idNota = id + '-nota';
        return (
          '<div class="reco-pregunta">' +
            '<p class="reco-texto">' + p + '</p>' +
            opcionesHtml(id) +
            '<div class="genea-pregunta" style="margin-top:10px;">' +
              '<label for="' + idNota + '">Aclaración o detalle (opcional) — nombres, fechas, lo que quieras recordar</label>' +
              '<textarea id="' + idNota + '" rows="2" placeholder="Ej.: fue mi tía Marta, en 1998…"></textarea>' +
            '</div>' +
          '</div>'
        );
      }).join('');
      return (
        '<div class="genea-grupo">' +
          '<h4>' + grupo.titulo + '</h4>' +
          nota +
          preguntas +
        '</div>'
      );
    }).join('');

    return (
      '<details class="perfil-card genea-tema-card" id="genea-test-tema-' + (temaIdx + 1) + '"' + (temaIdx === 0 ? ' open' : '') + '>' +
        '<summary>' +
          '<span class="perfil-nombre">' + tema.tema + '</span>' +
        '</summary>' +
        '<div class="perfil-cuerpo">' +
          '<p class="genea-contexto">' + tema.contexto + '</p>' +
          grupos +
        '</div>' +
      '</details>'
    );
  }

  function escapeAttr(texto) {
    return String(texto).replace(/&/g, '&amp;').replace(/"/g, '&quot;');
  }

  function respuestaDe(id) {
    var marcada = document.querySelector('input[name="' + id + '"]:checked');
    return marcada ? marcada.value : null;
  }

  function notaDe(id) {
    var campo = document.getElementById(id + '-nota');
    return campo && campo.value.trim() ? campo.value.trim() : '';
  }

  function escapeHtmlTexto(texto) {
    return String(texto).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  // ---- Ponderación por tema: cuenta cuántos "Sí" hay en cada uno de los 6 temas, para saber
  // en cuál tiene hoy más carga activa. Usa el mismo color por categoría de reco-quiz.js si está
  // disponible (colorPara(tipo, idx, total)), y si no, cae a un dorado fijo. ----
  function colorParaTema(idx, total) {
    return (window.AVC_RECO_QUIZ && window.AVC_RECO_QUIZ.colorPara)
      ? window.AVC_RECO_QUIZ.colorPara('tema', idx, total)
      : '#E0A92E';
  }

  function calcularPesoTemas() {
    if (!window.GENEALOGIA_TEST) return null;
    var temas = window.GENEALOGIA_TEST;
    var conteo = temas.map(function () { return 0; });
    var total = temas.map(function () { return 0; });
    temas.forEach(function (tema, temaIdx) {
      tema.grupos.forEach(function (grupo, grupoIdx) {
        grupo.preguntas.forEach(function (p, pIdx) {
          var id = preguntaId(temaIdx, grupoIdx, pIdx);
          total[temaIdx]++;
          if (respuestaDe(id) === 'Sí') conteo[temaIdx]++;
        });
      });
    });
    return { temas: temas, conteo: conteo, total: total };
  }

  function donutTemas(peso) {
    var sumaSi = peso.conteo.reduce(function (a, b) { return a + b; }, 0);
    if (!sumaSi) return '';
    var acc = 0;
    var partes = [];
    var leyenda = [];
    peso.temas.forEach(function (tema, i) {
      if (!peso.conteo[i]) return;
      var color = colorParaTema(i, peso.temas.length);
      var desde = (acc / sumaSi) * 360;
      acc += peso.conteo[i];
      var hasta = (acc / sumaSi) * 360;
      partes.push(color + ' ' + desde.toFixed(1) + 'deg ' + hasta.toFixed(1) + 'deg');
      leyenda.push('<li><span class="reco-resultado-punto" style="--dot-color:' + color + ';"></span>' + tema.tema + ' — ' + peso.conteo[i] + ' Sí</li>');
    });
    return (
      '<div class="reco-donut-wrap">' +
        '<div class="reco-donut" style="background:conic-gradient(' + partes.join(', ') + ');" aria-hidden="true"></div>' +
        '<ul class="reco-donut-legend">' + leyenda.join('') + '</ul>' +
      '</div>'
    );
  }

  var COLORES_IMPLICACION = ['#211A33', '#7A4C6E', '#C2607D', '#E0A92E', '#2c2344', '#E08FA0'];
  function colorParaImplicacion(idx) {
    return COLORES_IMPLICACION[idx % COLORES_IMPLICACION.length];
  }

  function calcularPesoImplicaciones() {
    if (!window.GENEALOGIA_TEST) return null;
    var nombres = [];
    var indice = {};
    var conteo = [];
    var total = [];
    window.GENEALOGIA_TEST.forEach(function (tema, temaIdx) {
      tema.grupos.forEach(function (grupo, grupoIdx) {
        var nombre = grupo.implicacion || 'Otras observaciones';
        if (!(nombre in indice)) {
          indice[nombre] = nombres.length;
          nombres.push(nombre);
          conteo.push(0);
          total.push(0);
        }
        var i = indice[nombre];
        grupo.preguntas.forEach(function (p, pIdx) {
          var id = preguntaId(temaIdx, grupoIdx, pIdx);
          total[i]++;
          if (respuestaDe(id) === 'Sí') conteo[i]++;
        });
      });
    });
    return { nombres: nombres, conteo: conteo, total: total };
  }

  function donutImplicaciones(peso) {
    var sumaSi = peso.conteo.reduce(function (a, b) { return a + b; }, 0);
    if (!sumaSi) return '';
    var acc = 0;
    var partes = [];
    var leyenda = [];
    peso.nombres.forEach(function (nombre, i) {
      if (!peso.conteo[i]) return;
      var color = colorParaImplicacion(i);
      var desde = (acc / sumaSi) * 360;
      acc += peso.conteo[i];
      var hasta = (acc / sumaSi) * 360;
      partes.push(color + ' ' + desde.toFixed(1) + 'deg ' + hasta.toFixed(1) + 'deg');
      leyenda.push('<li><span class="reco-resultado-punto" style="--dot-color:' + color + ';"></span>' + nombre + ' — ' + peso.conteo[i] + ' Sí</li>');
    });
    return (
      '<div class="reco-donut-wrap">' +
        '<div class="reco-donut" style="background:conic-gradient(' + partes.join(', ') + ');" aria-hidden="true"></div>' +
        '<ul class="reco-donut-legend">' + leyenda.join('') + '</ul>' +
      '</div>'
    );
  }

  function desgloseImplicacionesHtml() {
    var peso = calcularPesoImplicaciones();
    if (!peso) return '';
    var sumaSi = peso.conteo.reduce(function (a, b) { return a + b; }, 0);
    if (!sumaSi) return '';
    var barras = peso.nombres.map(function (nombre, i) {
      var pct = peso.total[i] ? Math.round((peso.conteo[i] / peso.total[i]) * 100) : 0;
      var color = colorParaImplicacion(i);
      return (
        '<div class="reco-resultado-fila">' +
          '<span class="reco-resultado-nombre"><span class="reco-resultado-punto" style="--dot-color:' + color + ';"></span>' + nombre + '</span>' +
          '<div class="reco-quiz-barra-fondo"><div class="reco-quiz-barra" style="width:' + pct + '%;background:' + color + ';"></div></div>' +
          '<span class="reco-resultado-conteo">' + peso.conteo[i] + '/' + peso.total[i] + '</span>' +
        '</div>'
      );
    }).join('');
    return (
      '<div class="reco-resultado-desglose">' +
        '<h4>Desglose por implicación sistémica</h4>' +
        '<p class="genea-nota">Las mismas respuestas, agrupadas ahora por el tipo de carga transgeneracional al que apuntan —lealtades invisibles, secretos y traumas heredados, lenguaje nuclear que sostiene un patrón— sin importar en qué tema aparecieron. Usa tus aclaraciones escritas en cada pregunta para ponderar qué tanto peso real le das a cada una.</p>' +
        donutImplicaciones(peso) + barras +
      '</div>'
    );
  }

  function renderResumenTemas() {
    var cont = document.getElementById('genea-resumen-temas');
    if (!cont) return;
    var peso = calcularPesoTemas();
    if (!peso) return;
    var respondidas = peso.total.reduce(function (a, b) { return a + b; }, 0);
    var contestadas = 0;
    peso.temas.forEach(function (tema, temaIdx) {
      tema.grupos.forEach(function (grupo, grupoIdx) {
        grupo.preguntas.forEach(function (p, pIdx) {
          if (respuestaDe(preguntaId(temaIdx, grupoIdx, pIdx))) contestadas++;
        });
      });
    });
    if (!contestadas) {
      cont.innerHTML = '<p class="genea-nota">A medida que vayas respondiendo Sí, No o No me identifico en los seis temas, aquí va apareciendo en cuál tienes hoy más carga activa.</p>';
      return;
    }

    var maxConteo = Math.max.apply(null, peso.conteo);
    var top = peso.temas
      .map(function (tema, i) { return { tema: tema, i: i, n: peso.conteo[i], total: peso.total[i] }; })
      .filter(function (t) { return t.n === maxConteo && maxConteo > 0; });

    var barras = peso.temas.map(function (tema, i) {
      var pct = peso.total[i] ? Math.round((peso.conteo[i] / peso.total[i]) * 100) : 0;
      var color = colorParaTema(i, peso.temas.length);
      return (
        '<div class="reco-resultado-fila">' +
          '<span class="reco-resultado-nombre"><span class="reco-resultado-punto" style="--dot-color:' + color + ';"></span>' + tema.tema + '</span>' +
          '<div class="reco-quiz-barra-fondo"><div class="reco-quiz-barra" style="width:' + pct + '%;background:' + color + ';"></div></div>' +
          '<span class="reco-resultado-conteo">' + peso.conteo[i] + '/' + peso.total[i] + '</span>' +
        '</div>'
      );
    }).join('');

    var destacados = top.length
      ? top.map(function (t, idx) {
          var color = colorParaTema(t.i, peso.temas.length);
          return (
            '<div class="reco-resultado-perfil" style="--cat-color:' + color + ';">' +
              '<div class="reco-resultado-titulo-fila"><span class="reco-resultado-swatch"></span><h3 class="mt-0" style="margin:0;">' + t.tema.tema + '</h3></div>' +
              '<p style="margin-top:10px;">' + t.tema.contexto + '</p>' +
            '</div>'
          );
        }).join('')
      : '';

    cont.innerHTML =
      '<div class="reco-resultado">' +
        '<span class="small-caps eyebrow-gap">Resumen orientativo</span>' +
        '<h3 class="mt-0">' + (top.length > 1 ? 'Hoy tienes carga activa en varios temas por igual' : 'El tema con más carga activa en tus respuestas hoy es:') + '</h3>' +
        destacados +
        '<div class="reco-resultado-desglose"><h4>Desglose por tema (' + respondidas + ' preguntas en total, ' + contestadas + ' respondidas)</h4>' + donutTemas(peso) + barras + '</div>' +
        desgloseImplicacionesHtml() +
        '<p class="genea-nota">Esto no es un diagnóstico ni mide "cuánto trauma tienes" — solo te muestra en qué tema tus respuestas de hoy tienen más presencia, para que sepas por dónde puede convenirte empezar en el taller.</p>' +
        '<div class="reco-cta-taller">' +
          '<h4>Tu siguiente paso: Taller Sanando con mi Linaje</h4>' +
          '<p>Lleva este resumen al taller — ahí, con tu árbol ya construido, trabajamos en vivo el tema que más resuene, tema por tema, con acompañamiento.</p>' +
          '<div class="reco-cta-botones">' +
            '<a href="mi-genealogia.html#genea-tema-1" class="btn btn-primary">Ver la guía del taller</a>' +
            '<a href="https://wa.me/573176435131?text=Hola%2C%20quiero%20inscribirme%20al%20taller%20Sanando%20con%20mi%20Linaje" target="_blank" rel="noopener" class="btn btn-outline-dark">Inscribirme por WhatsApp</a>' +
          '</div>' +
        '</div>' +
      '</div>';
  }

  function seccionAncestros() {
    var ancestros = window.AVC_ARBOL ? window.AVC_ARBOL.obtenerAncestros() : [];
    var diagrama = window.AVC_ARBOL_DIAGRAMA ? window.AVC_ARBOL_DIAGRAMA(ancestros) : '';
    var detectados = ancestros.filter(function (a) { return a.lealtad === 'Sí' || a.lenguaje === 'Sí'; });

    var filasAncestros = ancestros.map(function (a) {
      return (
        '<tr><td>' + escapeHtmlTexto(a.nombre) + '</td><td>' + escapeHtmlTexto(a.vinculo) + '</td>' +
        '<td>' + escapeHtmlTexto(a.memoria || '—') + '</td>' +
        '<td>' + (a.lealtad || '—') + '</td><td>' + (a.lenguaje || '—') + '</td></tr>'
      );
    }).join('');

    var alertas = detectados.length
      ? '<ul>' + detectados.map(function (a) {
          var motivos = [];
          if (a.lealtad === 'Sí') motivos.push('lealtad/deuda invisible');
          if (a.lenguaje === 'Sí') motivos.push('lenguaje nuclear o patrón repetido');
          return '<li><strong>' + escapeHtmlTexto(a.nombre) + '</strong> (' + escapeHtmlTexto(a.vinculo) + '): ' + motivos.join(' y ') + '.</li>';
        }).join('') + '</ul>'
      : '<p><em>No marcaste "Sí" en lealtad ni lenguaje nuclear para ningún ancestro — o aún no agregaste ancestros.</em></p>';

    return (
      '<section>' +
        '<h2>Tu árbol genealógico</h2>' +
        '<div class="arbol-wrap">' + diagrama + '</div>' +
        (ancestros.length ? '<table class="tabla-qya"><tr><th>Nombre</th><th>Vínculo</th><th>Por qué lo recuerdas</th><th>Lealtad</th><th>Lenguaje nuclear</th></tr>' + filasAncestros + '</table>' : '') +
        '<h4>Posibles lealtades familiares y lenguaje nuclear detectados</h4>' +
        alertas +
      '</section>' +
      '<section>' +
        '<h2>Contexto: epigenética del trauma heredado y lenguaje nuclear</h2>' +
        '<p>La epigenética estudia cómo el estrés y el trauma pueden activar marcas químicas que regulan la expresión de los genes, sin cambiar el ADN mismo — hay evidencia de que ciertos efectos del trauma pueden transmitirse más allá de quien lo experimentó primero, hacia hijos y nietos. "Lenguaje nuclear" nombra las palabras, frases o imágenes cargadas emocionalmente que una persona repite sin saber bien por qué; prestar atención a esas frases —y a la edad en que aparecieron ciertos miedos o síntomas— puede dar pistas sobre qué generación anterior vivió algo parecido. (Referencia: Wolynn, M. (2016). <em>Este dolor no es mío</em>. Ediciones Gaia — ver más en la página "Este dolor no es mío" del sitio).</p>' +
      '</section>' +
      '<section>' +
        '<h2>Cargas transgeneracionales</h2>' +
        '<p>Las lealtades invisibles llevan, sin decidirlo conscientemente, a repetir el destino de un antepasado como una forma inconsciente de pertenecer o de "aliviar" su sufrimiento. Reconocer de quién es una carga —y devolverla simbólicamente a quien pertenece, con respeto— es parte del trabajo del Taller Sanando con mi Linaje.</p>' +
      '</section>'
    );
  }

  function seccionPesoTemasReporte() {
    var peso = calcularPesoTemas();
    if (!peso) return '';
    var sumaSi = peso.conteo.reduce(function (a, b) { return a + b; }, 0);
    var maxConteo = Math.max.apply(null, peso.conteo);
    var top = peso.temas
      .map(function (tema, i) { return { tema: tema, i: i, n: peso.conteo[i], total: peso.total[i] }; })
      .filter(function (t) { return t.n === maxConteo && maxConteo > 0; });

    var acc = 0;
    var partes = [];
    var leyenda = [];
    peso.temas.forEach(function (tema, i) {
      if (!peso.conteo[i]) return;
      var color = colorParaTema(i, peso.temas.length);
      var desde = (acc / sumaSi) * 360;
      acc += peso.conteo[i];
      var hasta = (acc / sumaSi) * 360;
      partes.push(color + ' ' + desde.toFixed(1) + 'deg ' + hasta.toFixed(1) + 'deg');
      leyenda.push('<li><span class="punto" style="background:' + color + ';"></span>' + escapeHtmlTexto(tema.tema) + ' — ' + peso.conteo[i] + ' Sí</li>');
    });
    var donut = sumaSi ? (
      '<div class="donut-wrap">' +
        '<div class="donut" style="background:conic-gradient(' + partes.join(', ') + ');"></div>' +
        '<ul class="leyenda">' + leyenda.join('') + '</ul>' +
      '</div>'
    ) : '<p><em>Aún no marcaste ningún "Sí" en los seis temas.</em></p>';

    var barras = peso.temas.map(function (tema, i) {
      var pct = peso.total[i] ? Math.round((peso.conteo[i] / peso.total[i]) * 100) : 0;
      var color = colorParaTema(i, peso.temas.length);
      return (
        '<tr><td><span class="punto" style="background:' + color + ';"></span>' + escapeHtmlTexto(tema.tema) + '</td>' +
        '<td>' + peso.conteo[i] + '/' + peso.total[i] + '</td>' +
        '<td><div class="barra-fondo"><div class="barra" style="width:' + pct + '%;background:' + color + ';"></div></div></td></tr>'
      );
    }).join('');

    var destacados = top.map(function (t) {
      var color = colorParaTema(t.i, peso.temas.length);
      return '<div class="perfil" style="border-left:5px solid ' + color + ';"><h3><span class="swatch" style="background:' + color + ';"></span>' + escapeHtmlTexto(t.tema.tema) + '</h3><p>' + escapeHtmlTexto(t.tema.contexto) + '</p></div>';
    }).join('');

    var textoWsp = encodeURIComponent('Hola, quiero inscribirme al taller Sanando con mi Linaje. Hice el Test de Genealogía y el tema con más carga en mis respuestas fue: ' + (top.length ? top.map(function (t) { return t.tema.tema; }).join(' y ') : '(sin definir aún)') + '.');

    return (
      '<section>' +
        '<h2>Temas con más carga activa hoy</h2>' +
        '<p class="nota">Esto no es un diagnóstico ni mide "cuánto trauma tienes" — solo muestra en qué tema tus respuestas de hoy tienen más presencia, para que sepas por dónde puede convenirte empezar en el taller.</p>' +
        destacados +
        donut +
        (sumaSi ? '<table class="tabla-desglose">' + barras + '</table>' : '') +
        '<div class="cta-taller">' +
          '<h4>Tu siguiente paso: Taller Sanando con mi Linaje</h4>' +
          '<p>Lleva este resumen al taller — ahí, con tu árbol ya construido, trabajamos en vivo el tema que más resuene, con acompañamiento.</p>' +
          '<p><a href="https://www.aprenderavivircontigo.com/mi-genealogia.html" target="_blank" rel="noopener">Ver la guía del taller</a> &nbsp;·&nbsp; <a href="https://wa.me/573176435131?text=' + textoWsp + '" target="_blank" rel="noopener">Inscribirme por WhatsApp</a></p>' +
        '</div>' +
      '</section>'
    );
  }

  function seccionPesoImplicacionesReporte() {
    var peso = calcularPesoImplicaciones();
    if (!peso) return '';
    var sumaSi = peso.conteo.reduce(function (a, b) { return a + b; }, 0);
    if (!sumaSi) return '';
    var acc = 0;
    var partes = [];
    var leyenda = [];
    peso.nombres.forEach(function (nombre, i) {
      if (!peso.conteo[i]) return;
      var color = colorParaImplicacion(i);
      var desde = (acc / sumaSi) * 360;
      acc += peso.conteo[i];
      var hasta = (acc / sumaSi) * 360;
      partes.push(color + ' ' + desde.toFixed(1) + 'deg ' + hasta.toFixed(1) + 'deg');
      leyenda.push('<li><span class="punto" style="background:' + color + ';"></span>' + escapeHtmlTexto(nombre) + ' — ' + peso.conteo[i] + ' Sí</li>');
    });
    var donut = (
      '<div class="donut-wrap">' +
        '<div class="donut" style="background:conic-gradient(' + partes.join(', ') + ');"></div>' +
        '<ul class="leyenda">' + leyenda.join('') + '</ul>' +
      '</div>'
    );
    var barras = peso.nombres.map(function (nombre, i) {
      var pct = peso.total[i] ? Math.round((peso.conteo[i] / peso.total[i]) * 100) : 0;
      var color = colorParaImplicacion(i);
      return (
        '<tr><td><span class="punto" style="background:' + color + ';"></span>' + escapeHtmlTexto(nombre) + '</td>' +
        '<td>' + peso.conteo[i] + '/' + peso.total[i] + '</td>' +
        '<td><div class="barra-fondo"><div class="barra" style="width:' + pct + '%;background:' + color + ';"></div></div></td></tr>'
      );
    }).join('');
    return (
      '<section>' +
        '<h2>Ponderación por implicación sistémica</h2>' +
        '<p class="nota">Las mismas respuestas, agrupadas ahora por el tipo de carga transgeneracional al que apuntan —lealtades invisibles, secretos y traumas heredados, lenguaje nuclear que sostiene un patrón— sin importar en qué tema aparecieron. Revisa tus aclaraciones escritas en la sección siguiente para darle más peso real a cada una.</p>' +
        donut +
        '<table class="tabla-desglose">' + barras + '</table>' +
      '</section>'
    );
  }

  function seccionAclaracionesPorImplicacion() {
    if (!window.GENEALOGIA_TEST) return '';
    var porImplicacion = {};
    var orden = [];
    window.GENEALOGIA_TEST.forEach(function (tema, temaIdx) {
      tema.grupos.forEach(function (grupo, grupoIdx) {
        var implicacion = grupo.implicacion || 'Otras observaciones';
        grupo.preguntas.forEach(function (p, pIdx) {
          var id = preguntaId(temaIdx, grupoIdx, pIdx);
          var nota = notaDe(id);
          if (!nota) return;
          if (!porImplicacion[implicacion]) { porImplicacion[implicacion] = []; orden.push(implicacion); }
          porImplicacion[implicacion].push({ pregunta: p, respuesta: respuestaDe(id), nota: nota });
        });
      });
    });
    if (!orden.length) return '';
    var bloques = orden.map(function (implicacion) {
      var items = porImplicacion[implicacion].map(function (it) {
        return (
          '<li><strong>' + (it.respuesta || 'Sin responder') + '</strong> — ' + escapeHtmlTexto(it.pregunta) +
          '<br><em>' + escapeHtmlTexto(it.nota) + '</em></li>'
        );
      }).join('');
      return '<div class="perfil"><h3>' + escapeHtmlTexto(implicacion) + '</h3><ul>' + items + '</ul></div>';
    }).join('');
    return (
      '<section>' +
        '<h2>Mis aclaraciones, por implicación</h2>' +
        '<p class="nota">Cada aclaración que escribiste, agrupada por el tipo de dinámica sistémica al que corresponde la pregunta — lealtades invisibles, matrimonios invisibles, exclusiones, secretos y patrones. Llévalo así al taller para ubicar más rápido de qué conversar primero.</p>' +
        bloques +
      '</section>'
    );
  }

  function construirReporteHtml() {
    var fecha = new Date().toLocaleDateString('es-CO', { year: 'numeric', month: 'long', day: 'numeric' });
    var temas = '';
    if (window.GENEALOGIA_TEST) {
      temas = window.GENEALOGIA_TEST.map(function (tema, temaIdx) {
        var filas = [];
        tema.grupos.forEach(function (grupo, grupoIdx) {
          grupo.preguntas.forEach(function (p, pIdx) {
            var id = preguntaId(temaIdx, grupoIdx, pIdx);
            var respuesta = respuestaDe(id);
            var nota = notaDe(id);
            filas.push(
              '<tr><td>' + escapeHtmlTexto(grupo.implicacion || grupo.titulo) + '</td><td>' + escapeHtmlTexto(p) + '</td><td>' +
              (respuesta || '(sin responder aún)') + '</td><td>' + (nota ? escapeHtmlTexto(nota) : '—') + '</td></tr>'
            );
          });
        });
        return (
          '<section><h2>' + escapeHtmlTexto(tema.tema) + '</h2>' +
          '<p class="nota">' + escapeHtmlTexto(tema.contexto) + '</p>' +
          '<table class="tabla-qya"><tr><th>Implicación</th><th>Pregunta</th><th>Respuesta</th><th>Aclaración</th></tr>' + filas.join('') + '</table></section>'
        );
      }).join('');
    }

    return (
      '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">' +
      '<title>Mi árbol y respuestas — Test de Genealogía</title>' +
      '<style>' +
        'body{font-family:Georgia,serif;max-width:800px;margin:40px auto;padding:0 20px;color:#211a33;line-height:1.6;}' +
        'h1{font-family:Arial,sans-serif;} h2{font-family:Arial,sans-serif;border-top:3px solid #f5c343;padding-top:16px;margin-top:40px;} h4{font-family:Arial,sans-serif;}' +
        '.nota{font-size:0.85rem;color:#c76b8a;background:#fdf6ea;padding:10px 14px;border-radius:8px;}' +
        'table{border-collapse:collapse;width:100%;margin-bottom:18px;}' +
        '.tabla-qya th,.tabla-qya td{padding:6px 8px;border-bottom:1px solid #eee;font-size:0.85rem;text-align:left;vertical-align:top;}' +
        '.arbol-wrap{background:#fdf6ea;border-radius:10px;padding:20px;text-align:center;margin-bottom:18px;}' +
        '.arbol-generacion{display:flex;justify-content:center;gap:24px;margin-bottom:16px;flex-wrap:wrap;}' +
        '.arbol-rama{display:flex;gap:8px;border-top:2px solid #ddd;padding-top:10px;}' +
        '.arbol-persona{background:#fff;border-radius:8px;padding:8px 12px;font-size:0.82rem;box-shadow:0 1px 4px rgba(0,0,0,0.08);min-width:90px;}' +
        '.arbol-vacio{color:#bbb;font-style:italic;border:1px dashed #ccc;background:transparent;box-shadow:none;}' +
        '.arbol-persona-yo{background:#f5c343;font-weight:700;}' +
        '.arbol-padres{border-top:2px solid #ddd;padding-top:10px;}' +
        '.perfil{background:#fdf6ea;border-radius:10px;padding:18px 22px;margin-bottom:18px;}' +
        '.swatch{display:inline-block;width:14px;height:14px;border-radius:50%;margin-right:8px;vertical-align:middle;}' +
        '.punto{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:7px;}' +
        '.donut-wrap{display:flex;align-items:center;gap:24px;flex-wrap:wrap;margin:14px 0 22px;}' +
        '.donut{width:120px;height:120px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 5px #fff, 0 1px 6px rgba(33,26,51,0.15);}' +
        '.leyenda{list-style:none;padding:0;margin:0;font-size:0.85rem;}' +
        '.leyenda li{margin-bottom:6px;}' +
        '.tabla-desglose td{padding:6px 8px;border-bottom:1px solid #eee;font-size:0.9rem;}' +
        '.barra-fondo{background:#eee;border-radius:999px;height:10px;overflow:hidden;}' +
        '.barra{height:100%;}' +
        '.cta-taller{margin-top:18px;padding:18px 20px;border-radius:10px;background:linear-gradient(135deg,rgba(245,195,67,0.16),rgba(194,96,125,0.1));border:1px solid rgba(224,169,46,0.4);}' +
        '.cta-taller h4{margin-top:0;}' +
        '.cta-taller a{color:#a3315a;font-weight:bold;}' +
        'footer{margin-top:50px;font-size:0.8rem;color:#888;border-top:1px solid #ddd;padding-top:14px;}' +
      '</style></head><body>' +
      '<h1>Mi árbol y mis respuestas — Test de Genealogía</h1>' +
      '<p>Aprender a Vivir Contigo · aprenderavivircontigo.com<br>Descargado: ' + fecha + '</p>' +
      '<p class="nota">Este documento es privado y se generó solo en tu navegador — nunca se envió a ningún servidor. Llévalo al taller "Sanando con mi Linaje" si quieres profundizar en lo que escribiste.</p>' +
      seccionAncestros() +
      seccionPesoTemasReporte() +
      seccionPesoImplicacionesReporte() +
      seccionAclaracionesPorImplicacion() +
      temas +
      '<footer>Aprender a Vivir Contigo — Reconocer para reconciliar. www.aprenderavivircontigo.com</footer>' +
      '</body></html>'
    );
  }

  function descargarRespuestas() {
    var contenido = construirReporteHtml();
    var blob = new Blob([contenido], { type: 'text/html;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'mi-arbol-y-respuestas-genealogia.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(function () { URL.revokeObjectURL(url); }, 500);
  }

  document.addEventListener('DOMContentLoaded', function () {
    var cont = document.getElementById('lista-genealogia-test');
    if (cont && window.GENEALOGIA_TEST) {
      cont.innerHTML = window.GENEALOGIA_TEST.map(tarjetaTema).join('');
      cont.addEventListener('change', function (e) {
        if (e.target && e.target.matches('input[type="radio"]')) renderResumenTemas();
      });
      renderResumenTemas();
    }

    var btnDescargar = document.getElementById('btn-descargar-genealogia');
    if (btnDescargar) {
      btnDescargar.addEventListener('click', descargarRespuestas);
    }

    if (window.location.hash) {
      var objetivo = document.querySelector(window.location.hash);
      if (objetivo && objetivo.tagName === 'DETAILS') {
        objetivo.setAttribute('open', '');
        setTimeout(function () { objetivo.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
      }
    }
  });
})();
