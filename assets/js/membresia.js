// Aprender a Vivir Contigo — Renderiza el contenido del Club de Membresía
// a partir de window.CATALOGO_RECURSOS (ver assets/js/recursos.js).

(function () {
  'use strict';

  var GRUPOS = [
    { tipo: 'libro', titulo: 'Libros y guías descargables' },
    { tipo: 'blog', titulo: 'Artículos del blog' },
    { tipo: 'taller', titulo: 'Grabaciones de talleres' }
  ];

  function tarjetaRecurso(r) {
    var boton = r.url
      ? '<a class="btn btn-primary btn-block" href="' + r.url + '" target="_blank" rel="noopener">Descargar / Ver</a>'
      : '<span class="btn btn-outline-dark btn-block" style="opacity:0.6;pointer-events:none;">Próximamente</span>';
    return (
      '<div class="card" style="margin-bottom:18px;">' +
        '<h4 class="mt-0">' + r.titulo + '</h4>' +
        '<p>' + r.descripcion + '</p>' +
        boton +
      '</div>'
    );
  }

  document.addEventListener('DOMContentLoaded', function () {
    var cont = document.getElementById('lista-recursos-membresia');
    if (!cont) return;
    var catalogo = window.CATALOGO_RECURSOS || [];

    var html = '';
    GRUPOS.forEach(function (grupo) {
      var items = catalogo.filter(function (r) { return r.tipo === grupo.tipo; });
      if (!items.length) return;
      html += '<div style="margin-bottom:32px;">' +
        '<span class="small-caps eyebrow-gap">' + grupo.titulo + '</span>' +
        '<div class="grid-2" style="margin-top:14px;">' +
        items.map(tarjetaRecurso).join('') +
        '</div>' +
      '</div>';
    });

    cont.innerHTML = html || '<p>Muy pronto agregaremos aquí el contenido del club.</p>';
  });
})();
