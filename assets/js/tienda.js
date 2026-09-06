// Aprender a Vivir Contigo — Carrito de reservas + Widget de Checkout de Wompi
//
// IMPORTANTE para quien configure esto:
// 1) Reemplaza WOMPI_PUBLIC_KEY con tu llave pública real (la obtienes gratis al crear tu
//    cuenta en https://comercios.wompi.co — Configuración → Llaves API). Mientras dejes la
//    llave de prueba (pub_test_...), los pagos NO son reales.
// 2) Verifica en la documentación oficial de Wompi (https://docs.wompi.co) que los nombres
//    de los atributos "data-*" del widget sigan siendo los mismos antes de recibir pagos
//    reales — las pasarelas de pago a veces actualizan su API.
// 3) data-redirect-url debe ser una página real de tu sitio (por ejemplo gracias.html) a la
//    que Wompi devuelve al comprador después de pagar.

(function () {
  'use strict';

  var WOMPI_PUBLIC_KEY = 'pub_test_REEMPLAZAR_CON_TU_LLAVE'; // ← cámbiala por tu llave real de Wompi

  // Catálogo compartido con agenda-te.html — para agregar un taller nuevo, edita assets/js/catalogo.js
  var PRODUCTOS = window.CATALOGO_TALLERES || [];

  var carrito = {}; // { id: cantidad }

  function formatoCOP(valor) {
    return '$' + valor.toLocaleString('es-CO') + ' COP';
  }

  function calcularTotal() {
    var total = 0;
    PRODUCTOS.forEach(function (p) {
      total += (carrito[p.id] || 0) * p.precio;
    });
    return total;
  }

  function renderProductos() {
    var cont = document.getElementById('lista-productos');
    if (!cont) return;
    cont.innerHTML = PRODUCTOS.map(function (p) {
      var cant = carrito[p.id] || 0;
      return (
        '<div class="producto-fila">' +
          '<div class="producto-info">' +
            '<strong>' + p.nombre + '</strong>' +
            '<span>' + p.detalle + '</span>' +
          '</div>' +
          '<div class="producto-precio">' + formatoCOP(p.precio) + '</div>' +
          '<div class="producto-cantidad">' +
            '<button type="button" class="btn-cant" data-accion="restar" data-id="' + p.id + '" aria-label="Quitar uno">−</button>' +
            '<span class="cant-valor">' + cant + '</span>' +
            '<button type="button" class="btn-cant" data-accion="sumar" data-id="' + p.id + '" aria-label="Agregar uno">+</button>' +
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
    // Referencia legible sin usar Date.now() para evitar duplicados evidentes:
    // combina un prefijo fijo con los ids/cantidades del carrito.
    var partes = Object.keys(carrito).filter(function (id) { return carrito[id] > 0; })
      .map(function (id) { return id + 'x' + carrito[id]; });
    return 'avc-' + partes.join('-') + '-' + Math.floor(Math.random() * 100000);
  }

  function pagarConWompi() {
    var total = calcularTotal();
    if (total <= 0) return;

    var contenedor = document.getElementById('wompi-widget-contenedor');
    contenedor.innerHTML = ''; // limpia cualquier widget anterior

    if (WOMPI_PUBLIC_KEY.indexOf('REEMPLAZAR') !== -1) {
      contenedor.innerHTML =
        '<div class="form-status error" style="display:block;">' +
        'Falta configurar la llave pública de Wompi en assets/js/tienda.js (variable WOMPI_PUBLIC_KEY). ' +
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
