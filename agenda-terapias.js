// Aprender a Vivir Contigo — Agenda de terapias con disponibilidad real (MySQL) y pago con ePayco.
// Requiere que el visitante haya iniciado sesión (login.html) para reservar y pagar.
//
// Reemplaza la variable de abajo por tu llave pública real de ePayco cuando la tengas
// (Panel ePayco → Configuración → Integraciones/Llaves → Public Key).
(function () {
  'use strict';

  var EPAYCO_PUBLIC_KEY = 'REEMPLAZAR_CON_TU_PUBLIC_KEY_EPAYCO';
  var PRECIO_TERAPIA = 150000; // COP — ajusta si el precio varía por terapeuta
  var PRECIO_MEMBRESIA = 35000; // COP

  function epaycoConfigurado() { return EPAYCO_PUBLIC_KEY.indexOf('REEMPLAZAR') === -1; }

  function abrirCheckoutEpayco(opts) {
    if (!epaycoConfigurado() || typeof ePayco === 'undefined') {
      alert('El pago en línea aún no está activado. Escríbenos por WhatsApp para coordinar tu pago mientras tanto.');
      return;
    }
    var handler = ePayco.checkout.configure({ key: EPAYCO_PUBLIC_KEY, test: true });
    handler.open({
      name: opts.nombre,
      description: opts.descripcion,
      invoice: opts.invoice,
      currency: 'cop',
      amount: String(opts.monto),
      country: 'co',
      lang: 'es',
      response: 'https://aprenderavivircontigo.com/gracias.html',
      confirmation: 'https://aprenderavivircontigo.com/confirmacion-epayco.php'
    });
  }

  // ---- Membresía ----
  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btn-pagar-membresia');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var status = document.getElementById('membresia-pago-status');
      fetch('sesion-actual.php').then(function (r) { return r.json(); }).then(function (sesion) {
        if (!sesion || !sesion.usuario_id) {
          window.location.href = 'login.html';
          return;
        }
        abrirCheckoutEpayco({
          nombre: 'Membresía Aprender a Vivir Contigo',
          descripcion: 'Membresía mensual — acceso al Club de Membresía',
          invoice: 'AVC-MEM-' + sesion.usuario_id + '-' + Date.now(),
          monto: PRECIO_MEMBRESIA
        });
      }).catch(function () {
        if (status) { status.textContent = 'No pudimos verificar tu sesión, intenta de nuevo.'; status.className = 'form-status error'; }
      });
    });
  });

  // ---- Agenda de terapias ----
  document.addEventListener('DOMContentLoaded', function () {
    var selectTerapeuta = document.getElementById('terapeuta-select');
    var inputFecha = document.getElementById('fecha-select');
    var contFranjas = document.getElementById('franjas-disponibles');
    var franjasVacio = document.getElementById('franjas-vacio');
    var btnPagarCita = document.getElementById('btn-pagar-cita');
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
      btnPagarCita.disabled = true;
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
      btnPagarCita.disabled = false;
    });

    selectTerapeuta.addEventListener('change', cargarFranjas);
    inputFecha.addEventListener('change', cargarFranjas);

    btnPagarCita.addEventListener('click', function () {
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
          citaStatus.textContent = 'Cupo reservado por 10 minutos — completa el pago para confirmarlo.';
          citaStatus.className = 'form-status ok';
          abrirCheckoutEpayco({
            nombre: 'Terapia virtual — Aprender a Vivir Contigo',
            descripcion: 'Sesión de terapia virtual, ' + franjaSeleccionada,
            invoice: 'AVC-CITA-' + data.cita_id + '-' + Date.now(),
            monto: PRECIO_TERAPIA
          });
        })
        .catch(function () {
          citaStatus.textContent = 'No pudimos reservar el cupo, intenta de nuevo.';
          citaStatus.className = 'form-status error';
        });
    });

    // Fecha mínima seleccionable: hoy.
    inputFecha.min = new Date().toISOString().slice(0, 10);
    cargarTerapeutas();
  });
})();
