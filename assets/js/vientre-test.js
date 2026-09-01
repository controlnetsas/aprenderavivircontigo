// Aprender a Vivir Contigo — Test "Regresando al Vientre": cuestionario de autoindagación para
// reconocer conflictos inconscientes y no resueltos con la figura materna y la figura paterna.
//
// 30 preguntas cerradas (Sí / No / No me identifico), 15 sobre el vínculo con la madre y 15 sobre
// el vínculo con el padre, mezcladas y presentadas en orden aleatorio (no predecible) por el mismo
// motor de reco-quiz.js que usan el Test del Eneatipo y el de las Heridas de la Infancia. Al final,
// un algoritmo simple pondera cuántos "Sí" marcaste en cada categoría y muestra cuál vínculo
// concentra más carga hoy — no como diagnóstico, sino como invitación a reconocerlo.

window.VIENTRE_TEST_PREGUNTAS = [
  {
    tema: 'Vínculo con la Madre',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sientes que nunca fue suficiente lo que hiciste para ganarte la aprobación de tu madre?',
          '¿Te cuesta recibir cuidado, ayuda o cariño de las mujeres en tu vida, aunque lo necesites?',
          '¿Sientes resentimiento o rabia hacia tu madre que nunca le has dicho abiertamente?',
          '¿Idealizas a tu madre hasta el punto de no permitirte ver o nombrar lo que te dolió de ella?',
          '¿Sientes que tuviste que cuidar o sostener emocionalmente a tu madre desde niño o niña, en vez de que ella te cuidara a ti?',
          '¿Te cuesta pedir ayuda o mostrar vulnerabilidad frente a otras personas?',
          '¿Sientes que repites, sin quererlo, alguna actitud o frase de tu madre que juraste que no repetirías?',
          '¿Sientes un vacío o una distancia emocional con tu madre que no logras nombrar del todo?',
          '¿Te cuesta confiar en que la vida te sostiene o te cuida, como si algo básico de esa confianza te faltara?',
          '¿Sientes culpa cuando te alejas emocionalmente de tu madre, aunque sea sano hacerlo?',
          '¿Buscas en tus parejas o amistades algo que sientes que no recibiste de tu madre?',
          '¿Te ha costado convertirte en madre o padre —o imaginarte siéndolo— por miedo a repetir su historia?',
          '¿Sientes que hay algo de tu madre —una emoción, una carga, un dolor— que sientes que "no es tuyo" pero cargas de todas formas?',
          '¿Te cuesta decir "no" o poner límites con tu madre sin sentir culpa?',
          '¿Sientes que aún esperas, de alguna forma, que tu madre te vea o te reconozca como eres hoy?'
        ]
      }
    ]
  },
  {
    tema: 'Vínculo con el Padre',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sientes que nunca fue suficiente lo que hiciste para ganarte la aprobación de tu padre?',
          '¿Te cuesta recibir guía, reconocimiento o apoyo de figuras de autoridad (jefes, mentores, mayores)?',
          '¿Sientes resentimiento o rabia hacia tu padre que nunca le has dicho abiertamente?',
          '¿Idealizas a tu padre hasta el punto de no permitirte ver o nombrar lo que te dolió de él?',
          '¿Sientes que tuviste que ocupar un lugar de "hombre o mujer de la casa" que no te correspondía por tu edad?',
          '¿Te cuesta sentirte seguro o segura de tus propias decisiones sin la validación de otros?',
          '¿Sientes que repites, sin quererlo, alguna actitud o frase de tu padre que juraste que no repetirías?',
          '¿Sientes un vacío o una distancia emocional con tu padre que no logras nombrar del todo?',
          '¿Te cuesta sentir que tienes derecho a ocupar tu lugar, a tomar espacio o a triunfar sin culpa?',
          '¿Sientes culpa cuando te alejas emocionalmente de tu padre, aunque sea sano hacerlo?',
          '¿Buscas en tus parejas o en figuras de autoridad algo que sientes que no recibiste de tu padre?',
          '¿Te ha costado convertirte en padre o madre —o imaginarte siéndolo— por miedo a repetir su historia?',
          '¿Sientes que hay algo de tu padre —una emoción, una carga, un dolor— que sientes que "no es tuyo" pero cargas de todas formas?',
          '¿Te cuesta decir "no" o poner límites con tu padre sin sentir culpa?',
          '¿Sientes que aún esperas, de alguna forma, que tu padre te vea o te reconozca como eres hoy?'
        ]
      }
    ]
  }
];

window.VIENTRE_PERFILES = [
  {
    id: 'madre',
    nombre: 'Conflicto no resuelto con la Madre',
    frase: '"Mamá, te doy el lugar de mi madre. Yo soy pequeño o pequeña, y tú eres grande. Yo recibo, tú das."',
    manifestacion: 'Te cuesta recibir cuidado, pedir ayuda o mostrarte vulnerable. Puede aparecer como una desconfianza de fondo en que la vida te sostiene, o como una necesidad constante —nunca del todo saciada— de aprobación y cercanía.',
    origen: 'A menudo viene de una etapa muy temprana en la que, por la razón que sea, no pudiste recibir de tu madre lo que necesitabas — o, al contrario, tuviste que darle tú a ella algo que le hacía falta.',
    invitacion: 'Date un momento para reconocer, sin juzgarte, qué parte de esta historia sigue viva hoy en cómo te relacionas contigo y con los demás. El Taller Regresando al Vientre ofrece un espacio guiado para trabajar este vínculo desde el cuerpo y la mirada sistémica.'
  },
  {
    id: 'padre',
    nombre: 'Conflicto no resuelto con el Padre',
    frase: '"Papá, te doy el lugar de mi padre. Yo soy pequeño o pequeña, y tú eres grande. Yo recibo, tú das."',
    manifestacion: 'Te cuesta sentirte con derecho a tu propio lugar, a decidir o a triunfar sin culpa. Puede aparecer como una búsqueda constante de validación externa, o como dificultad para poner límites frente a figuras de autoridad.',
    origen: 'A menudo viene de una etapa muy temprana en la que la estructura, la guía o el reconocimiento que necesitabas de tu padre no llegó de la forma en que lo necesitabas — o tuviste que sostener un lugar que no te correspondía por tu edad.',
    invitacion: 'Date un momento para reconocer, sin juzgarte, qué parte de esta historia sigue viva hoy en cómo te relacionas contigo y con los demás. El Taller Regresando al Vientre ofrece un espacio guiado para trabajar este vínculo desde el cuerpo y la mirada sistémica.'
  }
];
