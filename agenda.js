// Aprender a Vivir Contigo — agenda.js
// Un solo archivo para las tres cosas de agenda-te.html: talleres (carrito), membresía y terapias.
// Sin pasarela de pago: todo dirige a WhatsApp para coordinar el pago (Nequi / Bre-B / efectivo).

(function () {
  'use strict';

  var WHATSAPP_NUMERO = '573176435131';

  function abrirWhatsApp(mensaje) {
    var url = 'https://wa.me/' + WHATSAPP_NUMERO + '?text=' + encodeURIComponent(mensaje);
    window.open(url, '_blank');
  }

  function obtenerSesion() {
    return fetch('sesion-actual.php').then(function (r) { return r.json(); });
  }

  // =========================================================
  // 1. TALLERES — carrito, confirmación por WhatsApp
  // =========================================================
  var PRODUCTOS = window.CATALOGO_TALLERES || [];
  var carrito = {};

  function formatoCOP(valor) { return '$' + valor.toLocaleString('es-CO') + ' COP'; }
  function soloValorCOP(valor) { return '$' + valor.toLocaleString('es-CO'); }

  function calcularTotalCarrito() {
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
          '<a href="https://wa.me/' + WHATSAPP_NUMERO + '?text=' + encodeURIComponent('Hola, quiero agendar: ' + p.nombre) + '" target="_blank" rel="noopener">escríbenos por WhatsApp</a></span>';

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
        renderResumenCarrito();
      });
    });
  }

  function renderResumenCarrito() {
    var total = calcularTotalCarrito();
    var elTotal = document.getElementById('carrito-total');
    var elVacio = document.getElementById('carrito-vacio');
    var btnConfirmar = document.getElementById('btn-pagar-taller');
    if (elTotal) elTotal.textContent = formatoCOP(total);
    if (elVacio) elVacio.style.display = total > 0 ? 'none' : 'block';
    if (btnConfirmar) btnConfirmar.disabled = total <= 0;
  }

  function resumenTalleresTexto() {
    var lineas = [];
    PRODUCTOS.forEach(function (p) {
      var cant = carrito[p.id] || 0;
      if (cant > 0) lineas.push(cant + 'x ' + p.nombre);
    });
    return lineas.join(', ');
  }

  function confirmarTalleres() {
    var total = calcularTotalCarrito();
    if (total <= 0) return;
    var mensaje = 'Hola, quiero confirmar mi inscripción: ' + resumenTalleresTexto() +
      '. Total: ' + formatoCOP(total) + '. ¿Cómo hago el pago?';
    abrirWhatsApp(mensaje);
  }

  function iniciarTalleres() {
    if (!document.getElementById('lista-productos-agenda')) return;
    renderProductos();
    renderResumenCarrito();
    var btnConfirmar = document.getElementById('btn-pagar-taller');
    if (btnConfirmar) btnConfirmar.addEventListener('click', confirmarTalleres);
  }

  // =========================================================
  // 2. MEMBRESÍA — confirmación por WhatsApp
  // =========================================================
  function iniciarMembresia() {
    var btn = document.getElementById('btn-pagar-membresia');
    if (!btn) return;
    var status = document.getElementById('epayco-membresia-status');

    btn.addEventListener('click', function () {
      obtenerSesion().then(function (sesion) {
        if (!sesion || !sesion.usuario_id) {
          window.location.href = 'login.html';
          return;
        }
        abrirWhatsApp('Hola, quiero activar mi membresía mensual del Club de Membresía. ¿Cómo hago el pago?');
      }).catch(function () {
        if (status) { status.textContent = 'No pudimos verificar tu sesión, intenta de nuevo.'; status.className = 'form-status error'; }
      });
    });
  }

  // =========================================================
  // 3. TERAPIAS — franjas reales + reserva temporal + confirmación por WhatsApp
  // =========================================================
  function iniciarTerapias() {
    var selectTerapeuta = document.getElementById('terapeuta-select');
    var inputFecha = document.getElementById('fecha-select');
    var contFranjas = document.getElementById('franjas-disponibles');
    var franjasVacio = document.getElementById('franjas-vacio');
    var btnConfirmarCita = document.getElementById('btn-pagar-cita');
    var citaStatus = document.getElementById('cita-status');
    if (!selectTerapeuta || !inputFecha || !contFranjas) return;

    var franjaSeleccionada = null;

    function cargarTerapeutas() {
      fetch('terapeutas.php').then(function (r) { return r.json(); }).then(function (lista) {
        selectTerapeuta.innerHTML = lista.map(function (t) {
          return '<option value="' + t.id + '">' + t.nombre + (t.especialidad ? ' — ' + t.especialidad : '') + '</option>';
        }).join('');
        cargarFranjas();
      }).catch(function () {
        selectTerapeuta.innerHTML = '<option value="">No se pudo cargar la lista de terapeutas</option>';
      });
    }

    function cargarFranjas() {
      var terapeutaId = selectTerapeuta.value;
      var fecha = inputFecha.value;
      contFranjas.innerHTML = '';
      franjaSeleccionada = null;
      btnConfirmarCita.disabled = true;
      if (!terapeutaId || !fecha) return;

      fetch('disponibilidad.php?terapeuta_id=' + encodeURIComponent(terapeutaId) + '&fecha=' + encodeURIComponent(fecha))
        .then(function (r) { return r.json(); })
        .then(function (franjas) {
          franjasVacio.style.display = franjas.length ? 'none' : 'block';
          contFranjas.innerHTML = franjas.map(function (hora) {
            return '<button type="button" class="btn btn-outline-dark franja-hora" data-hora="' + hora + '">' + hora + '</button>';
          }).join('');
        })
        .catch(function () { franjasVacio.style.display = 'block'; });
    }

    contFranjas.addEventListener('click', function (e) {
      var btn = e.target.closest('.franja-hora');
      if (!btn) return;
      contFranjas.querySelectorAll('.franja-hora').forEach(function (b) { b.classList.remove('btn-primary'); });
      btn.classList.add('btn-primary');
      franjaSeleccionada = inputFecha.value + ' ' + btn.getAttribute('data-hora') + ':00';
      btnConfirmarCita.disabled = false;
    });

    selectTerapeuta.addEventListener('change', cargarFranjas);
    inputFecha.addEventListener('change', cargarFranjas);

    btnConfirmarCita.addEventListener('click', function () {
      if (!franjaSeleccionada) return;
      citaStatus.textContent = 'Reservando tu cupo...';
      citaStatus.className = 'form-status';

      var body = new URLSearchParams({
        terapeuta_id: selectTerapeuta.value,
        fecha_hora: franjaSeleccionada
      });

      fetch('reservar-cita.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body })
        .then(function (r) {
          if (r.status === 401) { window.location.href = 'login.html'; return null; }
          return r.json();
        })
        .then(function (data) {
          if (!data) return;
          if (data.error) {
            citaStatus.textContent = data.error;
            citaStatus.className = 'form-status error';
            cargarFranjas();
            return;
          }
          citaStatus.textContent = 'Cupo reservado por 10 minutos. Confirma el pago por WhatsApp para que quede asegurado.';
          citaStatus.className = 'form-status ok';
          abrirWhatsApp('Hola, agendé una terapia virtual para el ' + franjaSeleccionada + ' (cita #' + data.cita_id + '). ¿Cómo hago el pago para confirmarla?');
        })
        .catch(function () {
          citaStatus.textContent = 'No pudimos reservar el cupo, intenta de nuevo.';
          citaStatus.className = 'form-status error';
        });
    });

    inputFecha.min = new Date().toISOString().slice(0, 10);
    cargarTerapeutas();
  }

  // =========================================================
  document.addEventListener('DOMContentLoaded', function () {
    iniciarTalleres();
    iniciarMembresia();
    iniciarTerapias();
  });
})();
