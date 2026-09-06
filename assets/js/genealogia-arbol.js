// Aprender a Vivir Contigo — Asistente paso a paso para construir el árbol genealógico dentro
// del Test de Genealogía. Vas agregando ancestros uno por uno (nombre, vínculo familiar, por qué
// lo recuerdas, y dos preguntas rápidas Sí/No/No me identifico sobre lealtad familiar y lenguaje
// nuclear/patrón heredado). La lista de ancestros queda disponible en window.AVC_ARBOL para que
// genealogia-descargar.js pueda dibujar el árbol y el reporte final.

(function () {
  'use strict';

  var VINCULOS = ['Padre', 'Madre', 'Abuelo paterno', 'Abuela paterna', 'Abuelo materno', 'Abuela materna', 'Hermano/a', 'Otro'];

  function escapeHtml(texto) {
    return String(texto).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function crearAsistente(contenedorId) {
    var cont = document.getElementById(contenedorId);
    if (!cont) return null;

    var ancestros = [];
    var modo = 'lista'; // 'lista' | 'formulario'

    function opcionesRapidas(nombreCampo, valorActual) {
      return ['Sí', 'No', 'No me identifico'].map(function (op) {
        var activa = valorActual === op ? ' reco-opcion-activa' : '';
        return '<button type="button" class="reco-quiz-btn' + activa + '" data-campo="' + nombreCampo + '" data-valor="' + op + '">' + op + '</button>';
      }).join('');
    }

    function renderFormulario(borrador) {
      cont.innerHTML =
        '<div class="card" style="max-width:560px;margin:0 auto;">' +
          '<h3 class="mt-0">Nuevo ancestro</h3>' +
          '<div class="formulario" style="margin-bottom:18px;">' +
            '<div><label>Nombre</label><input type="text" id="arbol-nombre" value="' + escapeHtml(borrador.nombre || '') + '" placeholder="Ej: Rosa Elena"></div>' +
            '<div><label>Vínculo familiar</label><select id="arbol-vinculo">' +
              VINCULOS.map(function (v) { return '<option value="' + v + '"' + (borrador.vinculo === v ? ' selected' : '') + '>' + v + '</option>'; }).join('') +
            '</select></div>' +
            '<div><label>¿Por qué lo recuerdas o qué representa para ti?</label><textarea id="arbol-memoria" rows="3" placeholder="Escribe aquí...">' + escapeHtml(borrador.memoria || '') + '</textarea></div>' +
          '</div>' +
          '<div class="reco-pregunta"><p class="reco-texto">¿Sientes una lealtad o deuda invisible con esta persona?</p>' +
            '<div class="reco-opciones" id="arbol-lealtad">' + opcionesRapidas('lealtad', borrador.lealtad) + '</div></div>' +
          '<div class="reco-pregunta"><p class="reco-texto">¿Alguna palabra, miedo o patrón tuyo se parece a algo que viviste o escuchaste de ella? (lenguaje nuclear)</p>' +
            '<div class="reco-opciones" id="arbol-lenguaje">' + opcionesRapidas('lenguaje', borrador.lenguaje) + '</div></div>' +
          '<div style="display:flex;gap:12px;margin-top:20px;">' +
            '<button type="button" class="btn btn-primary" id="arbol-guardar">Guardar ancestro</button>' +
            '<button type="button" class="btn btn-outline-dark" id="arbol-cancelar">Cancelar</button>' +
          '</div>' +
        '</div>';

      cont.querySelectorAll('#arbol-lealtad .reco-quiz-btn, #arbol-lenguaje .reco-quiz-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          // Guarda lo ya escrito antes de volver a dibujar el formulario, para no perderlo.
          borrador.nombre = document.getElementById('arbol-nombre').value;
          borrador.vinculo = document.getElementById('arbol-vinculo').value;
          borrador.memoria = document.getElementById('arbol-memoria').value;
          var campo = btn.getAttribute('data-campo');
          borrador[campo] = btn.getAttribute('data-valor');
          renderFormulario(borrador);
        });
      });
      document.getElementById('arbol-guardar').addEventListener('click', function () {
        borrador.nombre = document.getElementById('arbol-nombre').value.trim();
        borrador.vinculo = document.getElementById('arbol-vinculo').value;
        borrador.memoria = document.getElementById('arbol-memoria').value.trim();
        if (!borrador.nombre) {
          document.getElementById('arbol-nombre').style.borderColor = '#a3312f';
          return;
        }
        ancestros.push(borrador);
        modo = 'lista';
        renderLista();
      });
      document.getElementById('arbol-cancelar').addEventListener('click', function () {
        modo = 'lista';
        renderLista();
      });
    }

    function tarjetaAncestro(a, idx) {
      return (
        '<div class="card" style="margin-bottom:14px;">' +
          '<div style="display:flex;justify-content:space-between;align-items:start;gap:12px;">' +
            '<div>' +
              '<strong style="font-family:var(--font-titulos);color:var(--azul-noche);">' + escapeHtml(a.nombre) + '</strong>' +
              '<span style="display:block;font-size:0.82rem;color:var(--rosa-vinculo);">' + escapeHtml(a.vinculo) + '</span>' +
              (a.memoria ? '<p style="margin-top:8px;font-size:0.9rem;">' + escapeHtml(a.memoria) + '</p>' : '') +
              '<p style="margin-top:6px;font-size:0.8rem;color:var(--texto-cuerpo);">Lealtad: ' + (a.lealtad || 'sin responder') + ' · Lenguaje nuclear: ' + (a.lenguaje || 'sin responder') + '</p>' +
            '</div>' +
            '<button type="button" class="btn btn-outline-dark" data-quitar="' + idx + '" style="white-space:nowrap;">Quitar</button>' +
          '</div>' +
        '</div>'
      );
    }

    function renderLista() {
      var tarjetas = ancestros.map(tarjetaAncestro).join('');
      var vacio = ancestros.length === 0
        ? '<p style="color:var(--rosa-vinculo);font-style:italic;margin-bottom:18px;">Todavía no has agregado ningún ancestro.</p>'
        : '';

      cont.innerHTML =
        (ancestros.length ? '<div id="arbol-preview" style="margin-bottom:24px;"></div>' : '') +
        vacio + tarjetas +
        '<button type="button" class="btn btn-primary" id="arbol-agregar">' + (ancestros.length ? 'Agregar otro ancestro' : 'Agregar un ancestro') + '</button>';

      cont.querySelectorAll('[data-quitar]').forEach(function (btn) {
        btn.addEventListener('click', function () {
          ancestros.splice(parseInt(btn.getAttribute('data-quitar'), 10), 1);
          renderLista();
        });
      });
      document.getElementById('arbol-agregar').addEventListener('click', function () {
        modo = 'formulario';
        renderFormulario({});
      });

      var preview = document.getElementById('arbol-preview');
      if (preview) preview.innerHTML = window.AVC_ARBOL_DIAGRAMA ? window.AVC_ARBOL_DIAGRAMA(ancestros) : '';
    }

    renderLista();

    return { obtenerAncestros: function () { return ancestros; } };
  }

  // ---- Genera el HTML del diagrama del árbol a partir de la lista de ancestros ----
  // Se usa tanto en la vista previa dentro de la página como en el reporte descargable.
  window.AVC_ARBOL_DIAGRAMA = function (ancestros) {
    function porVinculo(v) { return ancestros.filter(function (a) { return a.vinculo === v; }); }
    function caja(lista, vacioTexto) {
      if (!lista.length) return '<div class="arbol-persona arbol-vacio">' + (vacioTexto || 'Sin registrar') + '</div>';
      return lista.map(function (a) { return '<div class="arbol-persona"><strong>' + escapeHtml(a.nombre) + '</strong></div>'; }).join('');
    }
    var otros = ancestros.filter(function (a) { return a.vinculo === 'Hermano/a' || a.vinculo === 'Otro'; });

    return (
      '<div class="arbol-genealogico">' +
        '<div class="arbol-generacion arbol-abuelos">' +
          '<div class="arbol-rama">' + caja(porVinculo('Abuelo paterno'), 'Abuelo paterno') + caja(porVinculo('Abuela paterna'), 'Abuela paterna') + '</div>' +
          '<div class="arbol-rama">' + caja(porVinculo('Abuelo materno'), 'Abuelo materno') + caja(porVinculo('Abuela materna'), 'Abuela materna') + '</div>' +
        '</div>' +
        '<div class="arbol-generacion arbol-padres">' + caja(porVinculo('Padre'), 'Padre') + caja(porVinculo('Madre'), 'Madre') + '</div>' +
        '<div class="arbol-generacion arbol-yo"><div class="arbol-persona arbol-persona-yo">Tú</div></div>' +
        (otros.length ? '<div class="arbol-otros"><span class="arbol-otros-titulo">Hermanos/otros vínculos:</span> ' + otros.map(function (a) { return escapeHtml(a.nombre); }).join(', ') + '</div>' : '') +
      '</div>'
    );
  };

  document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('arbol-genealogico-wizard')) {
      window.AVC_ARBOL = crearAsistente('arbol-genealogico-wizard');
    }
  });
})();
