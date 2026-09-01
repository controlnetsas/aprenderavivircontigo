// Aprender a Vivir Contigo — Agenda tu cita: elegir horario (Google Calendar) + pagar (Wompi)
// Usa el catálogo compartido en assets/js/catalogo.js — agrega talleres ahí, no aquí.

(function () {
  'use strict';

  var WOMPI_PUBLIC_KEY = 'pub_test_REEMPLAZAR_CON_TU_LLAVE'; // ← misma llave que en tienda.js — cámbiala por tu llave real de Wompi

  var PRODUCTOS = window.CATALOGO_TALLERES || [];
  var carrito = {};

  function formatoCOP(valor) {
    return '$' + valor.toLocaleString('es-CO') + ' COP';
  }

  function soloValorCOP(valor) {
    return '$' + valor.toLocaleString('es-CO');
  }

  function calcularTotal() {
    var total = 0;
    PRODUCTOS.forEach(function (p) { total += (carrito[p.id] || 0) * p.precio; });
    return total;
  }

  function renderProductos() {
    var cont = document.getElementById('lista-productos-agenda');
    if (!cont) return;
    cont.innerHTML = PRODUCTOS.map(function (p) {
      var cant = carrito[p.id] || 0;
      var botonAgendar = p.calendarUrl
        ? '<a class="btn btn-outline-dark btn-agendar" href="' + p.calendarUrl + '" target="_blank" rel="noopener">📅 Ver horarios y agendar</a>'
        : '<span class="agenda-pendiente">Agenda de este taller próximamente — ' +
          '<a href="https://wa.me/573176435131?text=' + encodeURIComponent('Hola, quiero agendar: ' + p.nombre) + '" target="_blank" rel="noopener">escríbenos por WhatsApp</a></span>';

      var incluye = (p.incluye || []).map(function (item) { return '<li>' + item + '</li>'; }).join('');

      return (
        '<div class="producto-agenda-card">' +
          '<div class="producto-agenda-head">' +
            '<span class="icono-taller" aria-hidden="true">' + (p.icono || '✨') + '</span>' +
            '<div>' +
              '<h4>' + p.nombre + '</h4>' +
              '<span class="producto-detalle-linea">' + p.detalle + '</span>' +
            '</div>' +
          '</div>' +
          (p.descripcion ? '<p class="producto-descripcion">' + p.descripcion + '</p>' : '') +
          (incluye ? '<ul class="producto-incluye">' + incluye + '</ul>' : '') +
          '<div class="producto-agenda-footer">' +
            '<div class="producto-precio-tag">' +
              '<span class="producto-precio-valor">' + soloValorCOP(p.precio) + '</span>' +
              '<span class="producto-precio-cop">COP</span>' +
            '</div>' +
            '<div class="producto-agenda-acciones">' +
              botonAgendar +
              '<div class="producto-cantidad">' +
                '<button type="button" class="btn-cant" data-accion="restar" data-id="' + p.id + '" aria-label="Quitar uno">−</button>' +
                '<span class="cant-valor">' + cant + '</span>' +
                '<button type="button" class="btn-cant" data-accion="sumar" data-id="' + p.id + '" aria-label="Agregar uno">+</button>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>'
      );
    }).join('');

    cont.querySelectorAll('.btn-cant').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-id');
        var accion = btn.getAttribute('data-accion');
        var actual = carrito[id] || 0;
        if (accion === 'sumar') actual += 1;
        if (accion === 'restar') actual = Math.max(0, actual - 1);
        carrito[id] = actual;
        renderProductos();
        renderResumen();
      });
    });
  }

  function renderResumen() {
    var total = calcularTotal();
    var elTotal = document.getElementById('carrito-total');
    var elVacio = document.getElementById('carrito-vacio');
    var btnPagar = document.getElementById('btn-pagar');
    if (elTotal) elTotal.textContent = formatoCOP(total);
    if (elVacio) elVacio.style.display = total > 0 ? 'none' : 'block';
    if (btnPagar) btnPagar.disabled = total <= 0;
  }

  function referenciaUnica() {
    var partes = Object.keys(carrito).filter(function (id) { return carrito[id] > 0; })
      .map(function (id) { return id + 'x' + carrito[id]; });
    return 'avc-' + partes.join('-') + '-' + Math.floor(Math.random() * 100000);
  }

  function pagarConWompi() {
    var total = calcularTotal();
    if (total <= 0) return;
    var contenedor = document.getElementById('wompi-widget-contenedor');
    contenedor.innerHTML = '';

    if (WOMPI_PUBLIC_KEY.indexOf('REEMPLAZAR') !== -1) {
      contenedor.innerHTML =
        '<div class="form-status error" style="display:block;">' +
        'Falta configurar la llave pública de Wompi en assets/js/agenda.js (variable WOMPI_PUBLIC_KEY). ' +
        'Mientras tanto, escríbenos por WhatsApp y coordinamos el pago manualmente.' +
        '</div>';
      return;
    }

    var form = document.createElement('form');
    var script = document.createElement('script');
    script.src = 'https://checkout.wompi.co/widget.js';
    script.setAttribute('data-render', 'button');
    script.setAttribute('data-public-key', WOMPI_PUBLIC_KEY);
    script.setAttribute('data-currency', 'COP');
    script.setAttribute('data-amount-in-cents', String(total * 100));
    script.setAttribute('data-reference', referenciaUnica());
    script.setAttribute('data-redirect-url', window.location.origin + '/gracias.html');
    form.appendChild(script);
    contenedor.appendChild(form);
  }

  document.addEventListener('DOMContentLoaded', function () {
    renderProductos();
    renderResumen();
    var btnPagar = document.getElementById('btn-pagar');
    if (btnPagar) btnPagar.addEventListener('click', pagarConWompi);
  });
})();
