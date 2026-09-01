// Aprender a Vivir Contigo — Hablando Contigo (Blog)
// Renderiza window.BLOG_POSTS (ver blog-posts.js) como tarjetas expandibles en hablando-contigo.html.

(function () {
  'use strict';

  function slug(texto) {
    return texto.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita tildes
      .replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
  }

  function tarjetaPost(post) {
    var parrafos = (post.cuerpo || []).map(function (p) { return '<p>' + p + '</p>'; }).join('');
    return (
      '<details class="perfil-card post-card" id="post-' + post.slug + '">' +
        '<summary>' +
          '<span class="post-categoria">' + post.categoria + '</span>' +
          '<span class="perfil-nombre">' + post.titulo + '</span>' +
          '<span class="post-fecha">' + post.fecha + '</span>' +
        '</summary>' +
        '<div class="perfil-cuerpo post-cuerpo">' +
          '<p class="perfil-resumen post-resumen">' + post.resumen + '</p>' +
          parrafos +
        '</div>' +
      '</details>'
    );
  }

  function renderFiltros(posts, contFiltros) {
    var categorias = [];
    posts.forEach(function (p) {
      if (categorias.indexOf(p.categoria) === -1) categorias.push(p.categoria);
    });

    contFiltros.innerHTML =
      '<button type="button" class="filtro-post is-activo" data-categoria="todas">Todas</button>' +
      categorias.map(function (c) {
        return '<button type="button" class="filtro-post" data-categoria="' + c + '">' + c + '</button>';
      }).join('');

    contFiltros.querySelectorAll('.filtro-post').forEach(function (btn) {
      btn.addEventListener('click', function () {
        contFiltros.querySelectorAll('.filtro-post').forEach(function (b) { b.classList.remove('is-activo'); });
        btn.classList.add('is-activo');
        var cat = btn.getAttribute('data-categoria');
        document.querySelectorAll('.post-card').forEach(function (card) {
          var visible = cat === 'todas' || card.getAttribute('data-categoria') === cat;
          card.style.display = visible ? '' : 'none';
        });
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var cont = document.getElementById('lista-posts-blog');
    var contFiltros = document.getElementById('filtros-blog');
    var posts = window.BLOG_POSTS || [];

    if (cont && posts.length) {
      cont.innerHTML = posts.map(tarjetaPost).join('');
      // asigna data-categoria a cada tarjeta para el filtro
      posts.forEach(function (post) {
        var el = document.getElementById('post-' + post.slug);
        if (el) el.setAttribute('data-categoria', post.categoria);
      });
    }

    if (contFiltros && posts.length) {
      renderFiltros(posts, contFiltros);
    }

    var elTotal = document.getElementById('total-posts-blog');
    if (elTotal) elTotal.textContent = posts.length;

    // Si se llega con un enlace directo a una publicación (ej. hablando-contigo.html#post-mi-slug),
    // abre esa tarjeta automáticamente, muestra su categoría en el filtro y desplaza hasta ella.
    if (window.location.hash) {
      var objetivo = document.querySelector(window.location.hash);
      if (objetivo && objetivo.tagName === 'DETAILS') {
        objetivo.setAttribute('open', '');
        setTimeout(function () { objetivo.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
      }
    }
  });
})();
