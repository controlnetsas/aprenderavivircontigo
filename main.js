// Aprender a Vivir Contigo — comportamiento común del sitio
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
      var expanded = nav.classList.contains('open');
      toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { nav.classList.remove('open'); });
    });
  }

  // Formulario de contacto: valida, abre WhatsApp con el mensaje listo (igual que antes)
  // y, en paralelo, guarda el contacto en el servidor (base de datos + HubSpot + Brevo)
  // vía procesar-inscripcion.php. Si ese guardado falla por cualquier razón, el envío
  // por WhatsApp de todas formas se completa con normalidad.
  var form = document.getElementById('form-contacto');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nombre = form.nombre.value.trim();
      var whatsapp = form.whatsapp.value.trim();
      var correo = form.correo.value.trim();
      var taller = form.taller.value;
      var mensaje = form.mensaje.value.trim();
      var status = document.getElementById('form-status');

      if (!nombre || !whatsapp || !mensaje) {
        status.textContent = 'Por favor completa nombre, WhatsApp y mensaje antes de enviar.';
        status.className = 'form-status error';
        return;
      }

      // Guarda el contacto en el servidor (no bloquea ni depende del resultado).
      try {
        var datos = new URLSearchParams({
          nombre: nombre,
          whatsapp: whatsapp,
          correo: correo,
          taller: taller,
          sitio_web: '' // campo trampa anti-spam, siempre vacío en un envío humano
        });
        fetch('procesar-inscripcion-ajax.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: datos,
          keepalive: true
        }).catch(function () { /* silencioso: WhatsApp sigue funcionando igual */ });
      } catch (err) { /* silencioso */ }

      var texto = 'Hola, soy ' + nombre + '. ';
      texto += 'Me interesa: ' + taller + '. ';
      if (correo) texto += 'Mi correo es ' + correo + '. ';
      texto += mensaje;
      texto += ' (WhatsApp de contacto: ' + whatsapp + ')';

      var url = 'https://wa.me/573176435131?text=' + encodeURIComponent(texto);
      status.textContent = 'Abriendo WhatsApp con tu mensaje listo para enviar...';
      status.className = 'form-status ok';
      window.open(url, '_blank');
      form.reset();
    });
  }

  // Si se llega a la página con un enlace #ancla (por ejemplo desde los
  // botones de "Tests gratuitos" en la portada), algunas páginas insertan
  // contenido dinámico por JS que cambia la altura por encima de esa ancla
  // después de que el navegador ya intentó hacer scroll. Corregimos el
  // scroll una vez que ese contenido dinámico ya se insertó, para que el
  // enlace siempre lleve al lugar correcto.
  if (location.hash) {
    setTimeout(function () {
      var destino = document.querySelector(location.hash);
      if (destino) destino.scrollIntoView({ block: 'start' });
    }, 0);
  }
});
