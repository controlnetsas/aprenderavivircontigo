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

  // Formulario de contacto: valida y arma un mensaje de WhatsApp / mailto
  // (el sitio es 100% estático; si más adelante agregan un backend en PHP,
  // este mismo formulario puede apuntar a ese script en vez de este JS).
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
