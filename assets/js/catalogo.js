// Aprender a Vivir Contigo — catálogo compartido de talleres y sesiones.
// Lo usan tanto agenda-te.html (agendar + pagar) como tienda.html (solo pagar).
//
// Para agregar un taller nuevo en el futuro: copia un bloque, cámbiale el id (único, sin
// espacios), nombre, detalle, descripción, ícono, lista "incluye" y precio. Aparecerá
// automáticamente en ambas páginas.
//
// calendarUrl: pega aquí el enlace de tu "Programación de citas" de Google Calendar para ese
// taller/sesión (Calendar → Crear → Programación de citas → Compartir esta página de reservas).
// Mientras quede en null, el botón "Ver horarios y agendar" muestra un aviso e invita a
// escribir por WhatsApp en su lugar — nadie ve un enlace roto.
//
// icono: un solo emoji, se usa como distintivo visual de la tarjeta (no requiere subir imágenes).

window.CATALOGO_TALLERES = [
  {
    id: 'linaje',
    nombre: 'Sanando con mi Linaje',
    detalle: 'Viernes, 7:00–9:00 p.m. · Virtual · Cupo 6 personas',
    descripcion: 'Encuentro virtual en grupo pequeño para mirar tu árbol genealógico y los patrones que se repiten en tu linaje, con seis temas sistémicos guiados paso a paso.',
    icono: '🌳',
    incluye: ['Guía de facilitación digital', 'Plantilla de árbol genealógico', 'Frases sanadoras'],
    precio: 70000,
    calendarUrl: null
  },
  {
    id: 'consulta-virtual',
    nombre: 'Constelación Virtual — Asunto específico',
    detalle: 'Martes, 6:00–8:00 p.m. · 2 horas',
    descripcion: 'Sesión individual y privada por videollamada para trabajar un asunto puntual de tu vida — una relación, una decisión, un patrón que se repite — con el enfoque sistémico de las constelaciones familiares.',
    icono: '🧭',
    incluye: ['Sesión 100% individual', 'Enfocada en tu asunto específico', 'Por videollamada'],
    precio: 150000,
    calendarUrl: null
  },
  {
    id: 'grupal-presencial',
    nombre: 'Constelación Grupal (Presencial)',
    detalle: 'Sábado, 9:00 a.m.–1:00 p.m. · 4 horas · 6 participantes',
    descripcion: 'Encuentro presencial en grupo reducido donde cada participante puede constelar su propio asunto y también ser representante en los procesos de los demás — una experiencia profunda de sostén colectivo.',
    icono: '🤝',
    incluye: ['Grupo de máximo 6 personas', '4 horas de proceso', 'Presencial'],
    precio: 150000,
    calendarUrl: null
  },
  {
    id: 'grupal-virtual',
    nombre: 'Constelación Grupal (Virtual)',
    detalle: 'Miércoles, 7:00–9:00 p.m. · 6 participantes',
    descripcion: 'La misma experiencia grupal de constelaciones, en formato virtual y a un precio más accesible — ideal si quieres vivir tu primera constelación antes de dar el paso a un taller presencial.',
    icono: '💻',
    incluye: ['Grupo de máximo 6 personas', 'Por videollamada', 'Precio reducido'],
    precio: 70000,
    calendarUrl: null
  },
  {
    id: 'reconoce-te-presencial',
    nombre: 'Taller Reconoce TE (Presencial)',
    detalle: 'Sábado, 9:00 a.m.–1:00 p.m. · 4 horas · 6 participantes',
    descripcion: 'Encuentro presencial en grupo pequeño para reconocer tu eneatipo, tu herida de la infancia y el patrón que sostienen en tu día a día, con ejercicios guiados de autoobservación.',
    icono: '🪞',
    incluye: ['Grupo de máximo 6 personas', '4 horas de proceso', 'Presencial'],
    precio: 150000,
    calendarUrl: null
  },
  {
    id: 'vientre',
    nombre: 'Regresando al Vientre',
    detalle: 'Encuentro grupal · Cupo limitado',
    descripcion: 'Un espacio guiado para reconocer y sanar los conflictos inconscientes con mamá y papá, con meditación de regreso al origen, ejercicios de reconciliación simbólica y frases sistémicas para devolver cargas que no te corresponden.',
    icono: '🤰',
    incluye: ['Meditación guiada de regreso al vientre', 'Ejercicios de reconciliación con madre y padre', 'Trabajo desde el cuerpo y la respiración'],
    precio: 70000,
    calendarUrl: null
  },
  {
    id: 'campestre',
    nombre: 'Jornada Campestre (Pasadía)',
    detalle: 'Sábado, 9:00 a.m.–9:00 p.m. · 12 horas · 6 participantes',
    descripcion: 'Un día completo en un entorno campestre que combina varios talleres en una sola jornada — para quienes quieren un proceso más profundo e intensivo.',
    icono: '🌿',
    incluye: ['Taller Regresando al Vientre', 'Clase de Chi Gong', 'Taller Reconocer TE', 'Alimentación Consciente', 'Fogata de cierre'],
    precio: 270000,
    calendarUrl: null
  }
];
