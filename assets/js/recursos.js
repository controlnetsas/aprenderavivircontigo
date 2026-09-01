// Aprender a Vivir Contigo — Catálogo de recursos del Club de Membresía.
//
// Fuente única de verdad para el contenido descargable del club (libros, blog, talleres
// grabados). Igual que "catalogo.js" con los talleres, para agregar un recurso nuevo solo
// edita este archivo — aparece automáticamente en membresia.html.
//
// Cómo conseguir la "url" de cada recurso (ver README para el paso a paso completo):
//  - Blog: publica el artículo en tu blog de WordPress y pega aquí el enlace del post.
//  - Libros / PDFs: sube el archivo en WordPress (Medios → Añadir nuevo) y pega aquí el
//    enlace directo del archivo que WordPress te genera (termina en .pdf).
//  - Talleres grabados: sube el video a WordPress (o a YouTube en modo "oculto") y pega
//    aquí ese enlace.
//
// Mientras un recurso tenga url: null, se muestra como "Próximamente" en vez de un botón roto.

window.CATALOGO_RECURSOS = [
  {
    id: 'libro-reconocete',
    tipo: 'libro',
    titulo: 'Reconócete: guía introductoria al Eneagrama',
    descripcion: 'Un e-book corto para profundizar en los eneatipos que reconociste en el Taller Reconócete — Módulo III.',
    url: null
  },
  {
    id: 'libro-heridas',
    tipo: 'libro',
    titulo: 'Las 5 heridas de la infancia: primeros pasos para sanar',
    descripcion: 'Guía descargable en PDF con ejercicios de autoobservación para cada herida.',
    url: null
  },
  {
    id: 'blog-linaje',
    tipo: 'blog',
    titulo: 'Qué es una constelación familiar (y qué no es)',
    descripcion: 'Artículo del blog: mitos comunes y qué esperar realmente de una sesión.',
    url: null
  },
  {
    id: 'blog-chi-gong',
    tipo: 'blog',
    titulo: 'Chi Gong para regular el sistema nervioso',
    descripcion: 'Artículo del blog con una práctica corta para hacer en casa.',
    url: null
  },
  {
    id: 'taller-grabado-vientre',
    tipo: 'taller',
    titulo: 'Grabación: Regresando al Vientre',
    descripcion: 'Grabación completa de una edición anterior del taller (uso personal).',
    url: null
  },
  {
    id: 'taller-grabado-alimentacion',
    tipo: 'taller',
    titulo: 'Grabación: Alimentación Consciente',
    descripcion: 'Grabación completa de una edición anterior del taller (uso personal).',
    url: null
  }
];
