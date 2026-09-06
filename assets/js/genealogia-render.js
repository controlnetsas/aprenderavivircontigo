// Aprender a Vivir Contigo — Renderiza el contenido del Taller de Genealogía "Sanando con mi Linaje"
// a partir de window.GENEALOGIA_TALLER (ver genealogia.js). Solo explicaciones: sin preguntas.

(function () {
  'use strict';

  function pasos(lista) {
    return '<ol class="pasos">' + lista.map(function (p) { return '<li>' + p + '</li>'; }).join('') + '</ol>';
  }

  function tarjetaTema(tema) {
    var ejercicios = tema.ejercicios.map(function (ej) {
      return (
        '<div class="perfil-etapa">' +
          '<h4>✦ ' + ej.titulo + '</h4>' +
          '<p><strong>Objetivo:</strong> ' + ej.objetivo + '</p>' +
          pasos(ej.procedimiento) +
        '</div>'
      );
    }).join('');

    var subseccion = tema.subseccion
      ? '<div class="perfil-etapa perfil-porque"><h4>' + tema.subseccion.titulo + '</h4><p>' + tema.subseccion.texto + '</p></div>'
      : '';

    var temasConsulta = tema.temasConsulta
      ? '<div class="perfil-etapa perfil-porque"><h4>Temas de mayor consulta</h4><p style="font-style:italic;margin-bottom:12px;">' + tema.temasConsulta.intro + '</p>' +
        tema.temasConsulta.items.map(function (it) { return '<p><strong>' + it.titulo + ':</strong> ' + it.texto + '</p>'; }).join('') +
        '</div>'
      : '';

    var notaFacilitador = tema.notaFacilitador
      ? '<div class="genea-nota">⟡ Aclaración para la persona facilitadora: ' + tema.notaFacilitador + '</div>'
      : '';

    return (
      '<details class="perfil-card" id="genea-tema-' + tema.numero + '">' +
        '<summary>' +
          '<span class="perfil-num">Tema ' + tema.numero + '</span>' +
          '<span class="perfil-nombre">' + tema.titulo + '</span>' +
        '</summary>' +
        '<div class="perfil-cuerpo">' +
          '<p class="perfil-resumen">' + tema.intro + '</p>' +
          subseccion +
          ejercicios +
          temasConsulta +
          notaFacilitador +
          '<blockquote>“' + tema.fraseCierre + '”</blockquote>' +
        '</div>' +
      '</details>'
    );
  }

  function tarjetaCategoriaFrases(cat, idx) {
    var frases = cat.frases.map(function (f) { return '<p>“' + f + '”</p>'; }).join('');
    return (
      '<details class="perfil-card" id="genea-frases-' + (idx + 1) + '">' +
        '<summary><span class="perfil-nombre">' + cat.titulo + '</span></summary>' +
        '<div class="perfil-cuerpo genea-frases-lista">' + frases + '</div>' +
      '</details>'
    );
  }

  document.addEventListener('DOMContentLoaded', function () {
    var d = window.GENEALOGIA_TALLER;
    if (!d) return;

    var elObjetivo = document.getElementById('genea-objetivo-general');
    if (elObjetivo) elObjetivo.textContent = d.objetivoGeneral;
    var elComoUsar = document.getElementById('genea-como-usar');
    if (elComoUsar) elComoUsar.textContent = d.comoUsarGuia;

    var elAcuerdos = document.getElementById('genea-acuerdos');
    if (elAcuerdos) {
      elAcuerdos.innerHTML = d.acuerdos.map(function (a, i) {
        return '<li><span class="icono">' + (i + 1) + '</span><div><strong>' + a.titulo + '</strong>' + a.texto + '</div></li>';
      }).join('');
    }

    var elParteIIntro = document.getElementById('genea-parteI-intro');
    if (elParteIIntro) elParteIIntro.textContent = d.parteI.intro;
    var elParteILogica = document.getElementById('genea-parteI-logica');
    if (elParteILogica) elParteILogica.textContent = d.parteI.logica;
    var elParteICaso = document.getElementById('genea-parteI-caso');
    if (elParteICaso) elParteICaso.textContent = d.parteI.casoEspecial;
    var elParteIRecoleccion = document.getElementById('genea-parteI-recoleccion');
    if (elParteIRecoleccion) elParteIRecoleccion.textContent = d.parteI.recoleccion;

    var elSimbolos = document.getElementById('genea-simbolos-body');
    if (elSimbolos) {
      elSimbolos.innerHTML = d.parteI.simbolos.map(function (s) {
        return '<tr><td data-label="Símbolo" style="font-family:var(--font-titulos);font-weight:700;">' + s.simbolo + '</td><td data-label="Significado">' + s.significado + '</td></tr>';
      }).join('');
    }

    var elTemas = document.getElementById('lista-temas-genealogia');
    if (elTemas) elTemas.innerHTML = d.temas.map(tarjetaTema).join('');

    var elCierreIntro = document.getElementById('genea-cierre-intro');
    if (elCierreIntro) elCierreIntro.textContent = d.cierre.intro;
    var elLogros = document.getElementById('genea-logros');
    if (elLogros) elLogros.innerHTML = d.cierre.logros.map(function (l) { return '<li>' + l + '</li>'; }).join('');
    var elCierreNota = document.getElementById('genea-cierre-nota');
    if (elCierreNota) elCierreNota.textContent = d.cierre.nota;

    var elFrasesIntro = document.getElementById('genea-frases-intro');
    if (elFrasesIntro) elFrasesIntro.textContent = d.frasesSanadoras.intro;
    var elFrasesContexto = document.getElementById('genea-frases-contexto');
    if (elFrasesContexto) elFrasesContexto.textContent = d.frasesSanadoras.contexto;
    var elComoUsarFrases = document.getElementById('genea-frases-como-usar');
    if (elComoUsarFrases) elComoUsarFrases.innerHTML = d.frasesSanadoras.comoUsar.map(function (i) { return '<li>' + i + '</li>'; }).join('');

    var elMeditIntro = document.getElementById('genea-meditacion-intro');
    if (elMeditIntro) elMeditIntro.textContent = d.frasesSanadoras.meditacion.intro;
    var elMeditPasos = document.getElementById('genea-meditacion-pasos');
    if (elMeditPasos) elMeditPasos.innerHTML = pasos(d.frasesSanadoras.meditacion.pasos);
    var elAmbiente = document.getElementById('genea-ambiente');
    if (elAmbiente) elAmbiente.innerHTML = d.frasesSanadoras.ambiente.map(function (i) { return '<li>' + i + '</li>'; }).join('');

    var elCategorias = document.getElementById('lista-frases-categorias');
    if (elCategorias) elCategorias.innerHTML = d.frasesSanadoras.categorias.map(tarjetaCategoriaFrases).join('');

    var elReferencias = document.getElementById('genea-referencias');
    if (elReferencias) elReferencias.innerHTML = d.referencias.map(function (r) { return '<li>' + r + '</li>'; }).join('');

    if (window.location.hash) {
      var objetivo = document.querySelector(window.location.hash);
      if (objetivo && objetivo.tagName === 'DETAILS') {
        objetivo.setAttribute('open', '');
        setTimeout(function () { objetivo.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
      }
    }
  });
})();
