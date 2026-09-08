// Aprender a Vivir Contigo — Ruleta de sorteo (2 cupos de constelación familiar)
// Lee assets/js/../../participantes-ruleta.csv (mismo nombre siempre) — Carlos lo reemplaza
// cada semana exportando su lista de Excel como CSV con ese nombre exacto.

(function () {
  'use strict';

  var COLORES = ['#211A33', '#C2607D', '#F5C343']; // azul noche, rosa vínculo, dorado esencia — se repiten en ese orden
  var CUPOS_A_SORTEAR = 2;
  var CSV_URL = 'participantes-ruleta.csv?_=' + Date.now(); // evita caché al recargar la lista

  var canvas, ctx, ruletaWrap;
  var participantes = [];      // lista completa cargada del CSV
  var enJuego = [];            // los que aún no han salido sorteados
  var ganadores = [];
  var rotacionActual = 0;
  var girando = false;

  function parseCSV(texto) {
    var lineas = texto.trim().split(/\r?\n/);
    var filas = lineas.map(function (l) { return l.split(','); });
    // Si la primera fila parece encabezado ("nombre", "whatsapp"), se descarta.
    if (filas.length && /nombre/i.test(filas[0][0] || '')) filas.shift();
    return filas
      .map(function (f) { return { nombre: (f[0] || '').trim(), whatsapp: (f[1] || '').trim() }; })
      .filter(function (p) { return p.nombre; });
  }

  function cargarParticipantes() {
    var status = document.getElementById('ruleta-status');
    return fetch(CSV_URL)
      .then(function (r) {
        if (!r.ok) throw new Error('No se pudo leer el archivo');
        return r.text();
      })
      .then(function (texto) {
        participantes = parseCSV(texto);
        enJuego = participantes.slice();
        ganadores = [];
        if (status) {
          status.textContent = participantes.length + ' participantes cargados esta semana.';
          status.className = 'form-status ok';
        }
        renderGanadores();
        dibujarRueda();
      })
      .catch(function () {
        if (status) {
          status.textContent = 'No se pudo cargar participantes-ruleta.csv. Verifica que el archivo exista en la raíz del sitio.';
          status.className = 'form-status error';
        }
      });
  }

  function dibujarRueda() {
    if (!canvas || !ctx) return;
    var n = enJuego.length;
    var cx = canvas.width / 2;
    var cy = canvas.height / 2;
    var radio = canvas.width / 2 - 6;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (n === 0) {
      ctx.fillStyle = '#211A33';
      ctx.font = '600 18px Poppins, sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText('Sin participantes', cx, cy);
      return;
    }

    var sliceAngle = (2 * Math.PI) / n;

    for (var i = 0; i < n; i++) {
      var inicio = i * sliceAngle;
      var fin = inicio + sliceAngle;

      ctx.beginPath();
      ctx.moveTo(cx, cy);
      ctx.arc(cx, cy, radio, inicio, fin);
      ctx.closePath();
      ctx.fillStyle = COLORES[i % COLORES.length];
      ctx.fill();
      ctx.strokeStyle = '#FDF9F2';
      ctx.lineWidth = 2;
      ctx.stroke();

      // Texto del nombre, rotado a lo largo del radio de la porción.
      ctx.save();
      ctx.translate(cx, cy);
      ctx.rotate(inicio + sliceAngle / 2);
      ctx.textAlign = 'right';
      ctx.fillStyle = '#FDF9F2';
      ctx.font = '600 ' + Math.max(11, Math.min(16, 220 / n)) + 'px Poppins, sans-serif';
      var nombreCorto = enJuego[i].nombre.length > 22 ? enJuego[i].nombre.slice(0, 20) + '…' : enJuego[i].nombre;
      ctx.fillText(nombreCorto, radio - 14, 5);
      ctx.restore();
    }
  }

  function girar() {
    if (girando) return;
    if (enJuego.length === 0) return;
    if (ganadores.length >= CUPOS_A_SORTEAR) return;

    girando = true;
    var btnGirar = document.getElementById('btn-girar-ruleta');
    if (btnGirar) btnGirar.disabled = true;

    var n = enJuego.length;
    var sliceAngleDeg = 360 / n;

    var vueltasExtra = 6 + Math.floor(Math.random() * 3); // 6 a 8 vueltas completas
    var offsetAleatorio = Math.random() * 360;
    var nuevaRotacion = rotacionActual + vueltasExtra * 360 + offsetAleatorio;

    ruletaWrap.style.transition = 'transform 4.5s cubic-bezier(0.17, 0.67, 0.16, 0.99)';
    ruletaWrap.style.transform = 'rotate(' + nuevaRotacion + 'deg)';
    rotacionActual = nuevaRotacion;

    setTimeout(function () {
      var efectiva = (360 - (rotacionActual % 360)) % 360;
      var indiceGanador = Math.floor(efectiva / sliceAngleDeg) % n;
      var ganador = enJuego[indiceGanador];

      ganadores.push(ganador);
      enJuego.splice(indiceGanador, 1);

      renderGanadores();
      mostrarGanadorModal(ganador);

      // Re-centra la rueda (sin animación) antes de volver a dibujarla con un participante menos.
      ruletaWrap.style.transition = 'none';
      rotacionActual = rotacionActual % 360;
      ruletaWrap.style.transform = 'rotate(' + rotacionActual + 'deg)';
      dibujarRueda();

      girando = false;
      if (btnGirar) {
        if (ganadores.length >= CUPOS_A_SORTEAR || enJuego.length === 0) {
          btnGirar.disabled = true;
          btnGirar.textContent = 'Sorteo completo';
        } else {
          btnGirar.disabled = false;
          btnGirar.textContent = 'Girar por el segundo cupo';
        }
      }
    }, 4600);
  }

  function renderGanadores() {
    var cont = document.getElementById('ruleta-ganadores');
    if (!cont) return;
    if (ganadores.length === 0) {
      cont.innerHTML = '<p class="carrito-vacio">Aún no hay ganadores.</p>';
      return;
    }
    cont.innerHTML = ganadores.map(function (g, i) {
      return '<div class="card" style="margin-bottom:12px;"><span class="small-caps eyebrow-gap">Cupo ' + (i + 1) + '</span><h3 class="mt-0">' + g.nombre + '</h3>' +
        (g.whatsapp ? '<p style="margin:0;">WhatsApp: ' + g.whatsapp + '</p>' : '') + '</div>';
    }).join('');
  }

  function mostrarGanadorModal(g) {
    var modal = document.getElementById('ruleta-modal');
    var texto = document.getElementById('ruleta-modal-texto');
    if (!modal || !texto) return;
    texto.textContent = '🎉 ' + g.nombre;
    modal.style.display = 'flex';
  }

  function cerrarModal() {
    var modal = document.getElementById('ruleta-modal');
    if (modal) modal.style.display = 'none';
  }

  function reiniciar() {
    if (girando) return;
    enJuego = participantes.slice();
    ganadores = [];
    rotacionActual = 0;
    ruletaWrap.style.transition = 'none';
    ruletaWrap.style.transform = 'rotate(0deg)';
    var btnGirar = document.getElementById('btn-girar-ruleta');
    if (btnGirar) { btnGirar.disabled = false; btnGirar.textContent = 'Girar la ruleta'; }
    renderGanadores();
    dibujarRueda();
  }

  document.addEventListener('DOMContentLoaded', function () {
    canvas = document.getElementById('ruleta-canvas');
    ruletaWrap = document.getElementById('ruleta-wrap');
    if (!canvas) return;
    ctx = canvas.getContext('2d');

    var btnGirar = document.getElementById('btn-girar-ruleta');
    var btnRecargar = document.getElementById('btn-recargar-ruleta');
    var btnReiniciar = document.getElementById('btn-reiniciar-ruleta');
    var btnCerrarModal = document.getElementById('btn-cerrar-ruleta-modal');

    if (btnGirar) btnGirar.addEventListener('click', girar);
    if (btnRecargar) btnRecargar.addEventListener('click', cargarParticipantes);
    if (btnReiniciar) btnReiniciar.addEventListener('click', reiniciar);
    if (btnCerrarModal) btnCerrarModal.addEventListener('click', cerrarModal);

    cargarParticipantes();
  });
})();
