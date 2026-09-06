// Aprender a Vivir Contigo — Renderiza el contenido del Taller Reconócete Módulo III
// a partir de window.RECONOCETE_ENEATIPOS y window.RECONOCETE_HERIDAS (ver reconocete.js).

(function () {
  'use strict';

  function slug(texto) {
    return texto.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita tildes
      .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  }

  function tarjetaEneatipo(e) {
    return (
      '<details class="perfil-card" id="eneatipo-' + e.n + '">' +
        '<summary>' +
          '<span class="perfil-num">Eneatipo ' + e.n + '</span>' +
          '<span class="perfil-nombre">' + e.nombre + '</span>' +
          '<span class="perfil-antidoto">Antídoto: ' + e.antidoto + '</span>' +
        '</summary>' +
        '<div class="perfil-cuerpo">' +
          '<p class="perfil-resumen">' + e.resumen + '</p>' +
          '<div class="perfil-etapa"><h4>En la infancia</h4><p>' + e.infancia + '</p></div>' +
          '<div class="perfil-etapa"><h4>En la juventud</h4><p>' + e.juventud + '</p></div>' +
          '<div class="perfil-etapa"><h4>En la adultez</h4><p>' + e.adultez + '</p></div>' +
          '<div class="perfil-etapa perfil-porque"><h4>¿Por qué se construyó este patrón?</h4><p>' + e.porque + '</p></div>' +
          '<div class="perfil-ejercicio"><h4>Ejercicio de resignificación (meditación + observación consciente)</h4><p>' + e.ejercicio + '</p></div>' +
        '</div>' +
      '</details>'
    );
  }

  function tarjetaHerida(h) {
    return (
      '<details class="perfil-card perfil-card-herida" id="herida-' + slug(h.nombre) + '">' +
        '<summary>' +
          '<span class="perfil-num">Herida</span>' +
          '<span class="perfil-nombre">' + h.nombre + ' · máscara "' + h.mascara + '"</span>' +
          '<span class="perfil-antidoto">Antídoto: ' + h.antidoto + '</span>' +
        '</summary>' +
        '<div class="perfil-cuerpo">' +
          '<p class="perfil-resumen">' + h.resumen + '</p>' +
          '<div class="perfil-etapa"><h4>En la infancia</h4><p>' + h.infancia + '</p></div>' +
          '<div class="perfil-etapa"><h4>En la juventud</h4><p>' + h.juventud + '</p></div>' +
          '<div class="perfil-etapa"><h4>En la adultez</h4><p>' + h.adultez + '</p></div>' +
          '<div class="perfil-etapa perfil-porque"><h4>¿Por qué se construyó esta herida?</h4><p>' + h.porque + '</p></div>' +
          '<div class="perfil-ejercicio"><h4>Ejercicio de resignificación (meditación + observación consciente)</h4><p>' + h.ejercicio + '</p></div>' +
        '</div>' +
      '</details>'
    );
  }

  document.addEventListener('DOMContentLoaded', function () {
    var contEneatipos = document.getElementById('lista-eneatipos-modulo3');
    var contHeridas = document.getElementById('lista-heridas-modulo3');

    if (contEneatipos && window.RECONOCETE_ENEATIPOS) {
      contEneatipos.innerHTML = window.RECONOCETE_ENEATIPOS.map(tarjetaEneatipo).join('');
    }
    if (contHeridas && window.RECONOCETE_HERIDAS) {
      contHeridas.innerHTML = window.RECONOCETE_HERIDAS.map(tarjetaHerida).join('');
    }

    // Si se llega con un enlace directo a un perfil (ej. reconocete-modulo-3.html#eneatipo-5),
    // abre esa tarjeta automáticamente y desplaza la página hasta ella.
    if (window.location.hash) {
      var objetivo = document.querySelector(window.location.hash);
      if (objetivo && objetivo.tagName === 'DETAILS') {
        objetivo.setAttribute('open', '');
        setTimeout(function () { objetivo.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
      }
    }
  });
})();
