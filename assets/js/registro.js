// Aprender a Vivir Contigo — Registro (CRM gratuito) + respuestas automáticas por correo.
//
// Este archivo centraliza CADA formulario del sitio que pide nombre/correo (Club de Membresía,
// Test de Genealogía, Test del Eneatipo y las Heridas, y el formulario de Contacto) y los conecta
// a dos servicios externos GRATUITOS, pensados para sitios estáticos (sin backend ni servidor):
//
// 1) CRM (HubSpot Forms) — guarda cada registro como un contacto nuevo en tu HubSpot gratis, para
//    que tengas TODOS los registros del sitio en un solo lugar (no solo en WhatsApp).
//    Crea tu cuenta en hubspot.com/products/crm, ve a Marketing → Formularios, crea un
//    formulario con los campos Nombre, Correo y Teléfono, y copia su Portal ID y Form ID
//    (Compartir → Insertar código). Pégalos abajo en HUBSPOT_PORTAL_ID y HUBSPOT_FORM_ID.
//
// 2) Correos automáticos (EmailJS) — crea tu cuenta gratis en emailjs.com, conecta tu Gmail/Outlook
//    como "Service", y crea DOS "Templates" (el plan gratis permite hasta 2):
//      a) "Bienvenida al Club" — para quien se une al Club de Membresía. Variables: {{nombre}}, {{correo}}.
//      b) "Confirmación" — plantilla compartida para el Test de Genealogía, el Test del Eneatipo y
//         las Heridas, y el formulario de Contacto. Variables: {{nombre}}, {{correo}}, {{motivo}}
//         (por ejemplo: "tu registro para el Test de Genealogía").
//    Copia el Service ID, los dos Template ID y el Public Key (Account → General) y pégalos abajo.
//
// Mientras dejes los valores REEMPLAZAR_..., nada se envía a ningún lado (los datos solo se
// guardan en el navegador de la persona mientras dura su visita) y el sitio sigue funcionando
// exactamente igual, simplemente sin el CRM ni los correos automáticos activos todavía.

(function () {
  'use strict';

  var HUBSPOT_PORTAL_ID = 'REEMPLAZAR_PORTAL_ID';
  var HUBSPOT_FORM_ID = 'REEMPLAZAR_FORM_ID';

  var EMAILJS_SERVICE_ID = 'REEMPLAZAR_SERVICE_ID';
  var EMAILJS_TEMPLATE_BIENVENIDA_ID = 'REEMPLAZAR_TEMPLATE_BIENVENIDA_ID';
  var EMAILJS_TEMPLATE_CONFIRMACION_ID = 'REEMPLAZAR_TEMPLATE_CONFIRMACION_ID';
  var EMAILJS_PUBLIC_KEY = 'REEMPLAZAR_PUBLIC_KEY';

  function configurado(v) { return !!v && v.indexOf('REEMPLAZAR') === -1; }

  // Mensajes cortos de "motivo" por origen, usados en la plantilla de Confirmación.
  var MOTIVOS = {
    'membresia': 'tu ingreso al Club de Membresía',
    'genealogia-test': 'tu registro para el Test de Genealogía y el taller Sanando con mi Linaje',
    'reconocete-test': 'tu registro para el Test del Eneatipo y las Heridas de la Infancia',
    'vientre-test': 'tu registro para el test y el Taller Regresando al Vientre',
    'contacto': 'tu mensaje de contacto'
  };

  window.AVC_REGISTRO = {
    datos: null,
    crmConfigurado: configurado(HUBSPOT_PORTAL_ID) && configurado(HUBSPOT_FORM_ID),
    emailBienvenidaConfigurado: configurado(EMAILJS_SERVICE_ID) && configurado(EMAILJS_TEMPLATE_BIENVENIDA_ID) && configurado(EMAILJS_PUBLIC_KEY),
    emailConfirmacionConfigurado: configurado(EMAILJS_SERVICE_ID) && configurado(EMAILJS_TEMPLATE_CONFIRMACION_ID) && configurado(EMAILJS_PUBLIC_KEY),

    guardar: function (nombre, correo, whatsapp, origen) {
      this.datos = { nombre: nombre, correo: correo, whatsapp: whatsapp || '', origen: origen || 'membresia' };
      this.enviarACRM();
    },

    enviarACRM: function () {
      if (!this.crmConfigurado || !this.datos) return;
      var nombre = this.datos.nombre.trim();
      var partesNombre = nombre.split(' ');
      var payload = {
        fields: [
          { name: 'firstname', value: partesNombre[0] || nombre },
          { name: 'lastname', value: partesNombre.slice(1).join(' ') },
          { name: 'email', value: this.datos.correo },
          { name: 'phone', value: this.datos.whatsapp }
        ],
        context: { pageUri: window.location.href, pageName: document.title }
      };
      fetch('https://api.hsforms.com/submissions/v3/integration/submit/' + HUBSPOT_PORTAL_ID + '/' + HUBSPOT_FORM_ID, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      }).catch(function (err) { console.warn('Aprender a Vivir Contigo: no se pudo enviar el registro al CRM.', err); });
    },

    // Correo de bienvenida + confirmación de ingreso al Club de Membresía (plantilla dedicada).
    // Devuelve { enviado: true } si se intentó el envío, o { enviado:false, motivo } si no.
    enviarBienvenidaMembresia: function (nombre, correo) {
      if (!correo) return { enviado: false, motivo: 'sin-correo' };
      if (!this.emailBienvenidaConfigurado) return { enviado: false, motivo: 'sin-configurar' };
      if (typeof emailjs === 'undefined') return { enviado: false, motivo: 'sdk-no-cargado' };

      try {
        emailjs.init(EMAILJS_PUBLIC_KEY);
        emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_BIENVENIDA_ID, {
          nombre: nombre,
          correo: correo
        }).catch(function (err) { console.warn('Aprender a Vivir Contigo: no se pudo enviar el correo de bienvenida al club.', err); });
        return { enviado: true };
      } catch (err) {
        console.warn('Aprender a Vivir Contigo: error al enviar el correo de bienvenida al club.', err);
        return { enviado: false, motivo: 'error' };
      }
    },

    // Correo de confirmación automático y genérico (plantilla compartida), usado por el Test de
    // Genealogía, el Test del Eneatipo y las Heridas, y el formulario de Contacto.
    // Devuelve { enviado: true } si se intentó el envío, o { enviado:false, motivo } si no.
    enviarConfirmacion: function (nombre, correo, origen) {
      if (!correo) return { enviado: false, motivo: 'sin-correo' };
      if (!this.emailConfirmacionConfigurado) return { enviado: false, motivo: 'sin-configurar' };
      if (typeof emailjs === 'undefined') return { enviado: false, motivo: 'sdk-no-cargado' };

      try {
        emailjs.init(EMAILJS_PUBLIC_KEY);
        emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_CONFIRMACION_ID, {
          nombre: nombre,
          correo: correo,
          motivo: MOTIVOS[origen] || 'tu registro en Aprender a Vivir Contigo'
        }).catch(function (err) { console.warn('Aprender a Vivir Contigo: no se pudo enviar el correo de confirmación.', err); });
        return { enviado: true };
      } catch (err) {
        console.warn('Aprender a Vivir Contigo: error al enviar el correo de confirmación.', err);
        return { enviado: false, motivo: 'error' };
      }
    }
  };

  // ---- Wiring del formulario de ingreso al Club de Membresía en membresia.html ----
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-membresia');
    if (!form) return;
    var status = document.getElementById('membresia-status');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nombre = form.nombre.value.trim();
      var correo = form.correo.value.trim();
      var whatsapp = form.whatsapp.value.trim();

      if (!nombre || !correo) {
        status.textContent = 'Por favor completa tu nombre y correo antes de continuar.';
        status.className = 'form-status error';
        return;
      }

      window.AVC_REGISTRO.guardar(nombre, correo, whatsapp, 'membresia');
      window.AVC_REGISTRO.enviarBienvenidaMembresia(nombre, correo);

      status.textContent = '¡Bienvenido/a al club! Te enviamos un correo de confirmación.';
      status.className = 'form-status ok';

      var gate = document.getElementById('membresia-gate');
      var contenido = document.getElementById('contenido-membresia');
      if (gate) gate.style.display = 'none';
      if (contenido) {
        contenido.style.display = '';
        contenido.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---- Wiring del registro previo al Test de Genealogía en test-genealogia.html / mi-genealogia.html / genealogia.html ----
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-genealogia-test');
    if (!form) return;
    var status = document.getElementById('genealogia-test-status');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nombre = form.nombre.value.trim();
      var correo = form.correo.value.trim();
      var whatsapp = form.whatsapp.value.trim();

      if (!nombre || !correo) {
        status.textContent = 'Por favor completa tu nombre y correo antes de continuar.';
        status.className = 'form-status error';
        return;
      }

      window.AVC_REGISTRO.guardar(nombre, correo, whatsapp, 'genealogia-test');
      window.AVC_REGISTRO.enviarConfirmacion(nombre, correo, 'genealogia-test');

      status.textContent = '¡Gracias, ' + nombre.split(' ')[0] + '! Ya puedes hacer el test.';
      status.className = 'form-status ok';

      var gate = document.getElementById('genealogia-test-gate');
      var contenido = document.getElementById('contenido-genealogia-test');
      if (gate) gate.style.display = 'none';
      if (contenido) {
        contenido.style.display = '';
        contenido.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---- Wiring del registro previo al Test del Eneatipo y de las Heridas en descubrete.html ----
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-reconocete-test');
    if (!form) return;
    var status = document.getElementById('reconocete-test-status');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nombre = form.nombre.value.trim();
      var correo = form.correo.value.trim();
      var whatsapp = form.whatsapp.value.trim();

      if (!nombre || !correo) {
        status.textContent = 'Por favor completa tu nombre y correo antes de continuar.';
        status.className = 'form-status error';
        return;
      }

      window.AVC_REGISTRO.guardar(nombre, correo, whatsapp, 'reconocete-test');
      window.AVC_REGISTRO.enviarConfirmacion(nombre, correo, 'reconocete-test');

      status.textContent = '¡Gracias, ' + nombre.split(' ')[0] + '! Ya puedes hacer el test.';
      status.className = 'form-status ok';

      var gate = document.getElementById('reconocete-test-gate');
      var contenido = document.getElementById('contenido-reconocete-test');
      if (gate) gate.style.display = 'none';
      if (contenido) {
        contenido.style.display = '';
        contenido.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---- Wiring del registro previo al Test Regresando al Vientre en regresando-al-vientre.html ----
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-vientre-test');
    if (!form) return;
    var status = document.getElementById('vientre-test-status');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nombre = form.nombre.value.trim();
      var correo = form.correo.value.trim();
      var whatsapp = form.whatsapp.value.trim();

      if (!nombre || !correo) {
        status.textContent = 'Por favor completa tu nombre y correo antes de continuar.';
        status.className = 'form-status error';
        return;
      }

      window.AVC_REGISTRO.guardar(nombre, correo, whatsapp, 'vientre-test');
      window.AVC_REGISTRO.enviarConfirmacion(nombre, correo, 'vientre-test');

      status.textContent = '¡Gracias, ' + nombre.split(' ')[0] + '! Ya puedes hacer el test.';
      status.className = 'form-status ok';

      var gate = document.getElementById('vientre-test-gate');
      var contenido = document.getElementById('contenido-vientre-test');
      if (gate) gate.style.display = 'none';
      if (contenido) {
        contenido.style.display = '';
        contenido.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---- Wiring del formulario de Contacto en contacto.html ----
  // No interfiere con el envío del mensaje por WhatsApp (eso lo maneja main.js aparte): esto solo
  // guarda el contacto en el CRM y dispara el correo automático de confirmación, en paralelo.
  // Se usa la fase de "captura" (tercer argumento true) para leer los campos ANTES de que el
  // manejador de main.js limpie el formulario (form.reset()) al terminar de abrir WhatsApp.
  document.addEventListener('submit', function (e) {
    if (!e.target || e.target.id !== 'form-contacto') return;
    var form = e.target;
    var nombre = form.nombre.value.trim();
    var correo = form.correo.value.trim();
    var whatsapp = form.whatsapp.value.trim();

    if (!nombre || !whatsapp) return;

    window.AVC_REGISTRO.guardar(nombre, correo, whatsapp, 'contacto');
    if (correo) window.AVC_REGISTRO.enviarConfirmacion(nombre, correo, 'contacto');
  }, true);
})();
