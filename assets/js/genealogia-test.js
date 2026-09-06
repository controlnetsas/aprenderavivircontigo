// Aprender a Vivir Contigo — Test de Genealogía: cuestionario de autoindagación de tu linaje,
// basado en las preguntas de indagación del Taller de Genealogía "Sanando con mi Linaje".
//
// No es un test que se puntúa ni que arroja un "resultado": es un cuestionario guiado de
// autoobservación. Las respuestas se escriben y se quedan solo en el navegador de la persona
// (no se envían a ningún servidor) — al final puede descargarlas en un archivo de texto para
// llevarlas al taller presencial o virtual.

window.GENEALOGIA_TEST = [
  {
    tema: 'Tema 1 · La Sexualidad y el Embarazo',
    contexto: 'La sexualidad no es solo un hecho biológico: es un canal por el que la vida y la historia familiar fluyen de una generación a otra.',
    grupos: [
      {
        titulo: 'El relato de mi llegada',
        implicacion: 'Lealtad invisible',
        preguntas: [
          '¿Sientes que hubo palabras o silencios especiales alrededor de tu llegada a la familia (por ejemplo, si fuiste un embarazo esperado, sorpresa, o vivido con dificultad)?',
          '¿Sientes que pudiste haber cargado, sin saberlo, alguna lealtad familiar relacionada con tu llegada?',
          '¿Sientes que hay algo que aún necesitarías reconocer para sentirte plenamente recibido o recibida en tu familia?'
        ]
      },
      {
        titulo: 'Honrar lo no nacido',
        implicacion: 'Exclusión',
        preguntas: [
          '¿Sabes si hubo, antes o después de ti, algún embarazo que no llegó a término en tu familia (aborto, pérdida, hijo o hija fallecidos)?',
          '¿Sientes que ese tema se mantuvo en silencio en tu familia hasta hoy?'
        ]
      },
      {
        titulo: 'Otros temas de consulta frecuente',
        implicacion: 'Secreto familiar',
        nota: 'Se presentan solo como puerta de entrada a la toma de conciencia — ninguno debe profundizarse sin acompañamiento profesional especializado.',
        preguntas: [
          '¿Hay abuso —físico, emocional o sexual— en tu historia familiar, propio o heredado, que nunca se ha nombrado abiertamente?',
          '¿Ha habido límites confusos entre generaciones en tu familia (por ejemplo, roles invertidos entre padres e hijos, o hijos que asumieron el rol de pareja de uno de sus padres)?',
          '¿Sientes que tu forma de amar o tu identidad han sido vistas y aceptadas en tu sistema familiar?'
        ]
      }
    ]
  },
  {
    tema: 'Tema 2 · Conflictos Intergeneracionales y su Impacto',
    contexto: 'Los conflictos que una generación no resuelve no desaparecen: buscan resolución, de manera inconsciente, a través de quienes vienen después.',
    grupos: [
      {
        titulo: 'Tu lenguaje nuclear',
        implicacion: 'Patrón repetitivo / lenguaje nuclear',
        preguntas: [
          '¿Usas repetidamente ciertas palabras o frases para describir cómo te sientes, sin saber muy bien de dónde vienen?',
          '¿Tienes miedos o síntomas recurrentes que no logras explicar con tu propia historia de vida?',
          '¿Recuerdas frases o dichos que se repetían mucho en tu familia cuando eras niño o niña?',
          '¿La edad en que comenzó ese síntoma o miedo coincide con la edad en que algo significativo le ocurrió a un familiar en tu árbol?',
          '¿Hay algo que sientes que "no es tuyo", pero que cargas de todas formas?'
        ]
      },
      {
        titulo: 'Patrones repetitivos en tu árbol',
        implicacion: 'Patrón repetitivo / lenguaje nuclear',
        preguntas: [
          '¿Identificas algún patrón —una profesión, una enfermedad, una forma de relación— que se repite en varias líneas de tu árbol?',
          '¿Sientes que hay un área de tu vida donde "algo se repite" sin que lo hayas elegido?',
          '¿Sientes que aún hay algo por reconocer para que ese patrón deje de repetirse a través de ti?'
        ]
      }
    ]
  },
  {
    tema: 'Tema 3 · Herencia Transgeneracional y Secretos Familiares',
    contexto: 'Los secretos no desaparecen por no nombrarse: generan un vacío en la conciencia del sistema familiar que las siguientes generaciones intentan llenar, sin saberlo, a través de síntomas o comportamientos.',
    grupos: [
      {
        titulo: 'Detective de silencios familiares',
        implicacion: 'Secreto familiar',
        preguntas: [
          '¿Hay algún nombre, historia o época que "no se toca" en tu familia?',
          '¿Sabes de algún crimen, hecho violento o muerte violenta en tu historia familiar del que casi no se habla?',
          '¿Sientes que algún comportamiento tuyo o de un familiar cercano podría estar "hablando" en lugar de ese silencio?'
        ]
      },
      {
        titulo: 'Ritual simbólico de reconocimiento',
        implicacion: 'Secreto familiar',
        preguntas: [
          '¿Sientes que darle un lugar a ese silencio, aunque sea solo para ti, te liberaría algo?',
          '¿Sientes que necesitarías acompañamiento profesional si más adelante decides hablar esto con tu familia?'
        ]
      }
    ]
  },
  {
    tema: 'Tema 4 · Cargas y Lealtades Transgeneracionales',
    contexto: 'Las lealtades invisibles llevan, sin decidirlo conscientemente, a repetir el destino de un antepasado como una forma inconsciente de pertenecer o de "aliviar" su sufrimiento.',
    grupos: [
      {
        titulo: 'Exclusiones y lealtades en tu árbol',
        implicacion: 'Lealtad invisible',
        preguntas: [
          '¿Hay alguien en tu árbol familiar que fue excluido, olvidado o del que casi no se habla (por ejemplo, por salir de la familia, un hijo o hija fuera del matrimonio, o por haber sido "deshonrado" o alejado)?',
          '¿Sabes si tu padre o tu madre tuvo, antes de la relación de la que naciste, una pareja, un matrimonio o un vínculo amoroso importante del que casi no se habla (un "matrimonio invisible")?',
          '¿Sientes que te pareces a alguien de tu árbol familiar en esta situación en particular?',
          '¿Puedes reconocer, con algo de claridad, de quién podría ser originalmente esta carga?',
          '¿Estás dispuesto o dispuesta a devolverla simbólicamente a quien pertenece, con respeto?'
        ]
      },
      {
        titulo: 'Frase sistémica de devolución',
        implicacion: 'Lealtad invisible',
        preguntas: [
          '¿Sentiste algo en el cuerpo —alivio, calor, una tensión que se suelta— al decir estas frases?',
          '¿Sientes que algo cambió en tu relación con esa carga después de este ejercicio?'
        ]
      }
    ]
  },
  {
    tema: 'Tema 5 · Liberación de Patrones Familiares',
    contexto: 'Liberarse no es rechazar el pasado, sino integrarlo desde el amor y el respeto, reconociendo que los ancestros hicieron lo mejor que pudieron con los recursos que tenían.',
    grupos: [
      {
        titulo: 'Carta de comprensión a un ancestro',
        implicacion: 'Integración y cierre',
        preguntas: [
          '¿Reconoces que esa persona no tuvo recursos —educación, apoyo, información— que tú sí tienes hoy?',
          '¿Reconocer eso te ayuda a soltar un poco el juicio hacia esa persona?'
        ]
      },
      {
        titulo: 'Ritual de integración y compromiso',
        implicacion: 'Integración y cierre',
        preguntas: [
          '¿Sientes que tu cuerpo —postura, respiración— cambia al decir esta frase?',
          '¿Tienes claro cómo notarías, en tu vida diaria, que estás viviendo con mayor libertad de estas cargas?'
        ]
      }
    ]
  },
  {
    tema: 'Tema 6 · El Flujo de la Vida — Cierre Integrador',
    contexto: 'Cuando cada persona ocupa su lugar, se honra lo que fue y se equilibra el dar y el recibir, el amor puede fluir con mayor libertad a través de las generaciones.',
    grupos: [
      {
        titulo: 'Círculo del flujo de la vida',
        implicacion: 'Integración y cierre',
        preguntas: [
          '¿Sientes que reconoces algo nuevo de tu historia familiar después de este recorrido?',
          '¿Sientes gratitud u honra hacia quienes vinieron antes que tú, incluso con lo difícil que vivieron?',
          '¿Sientes que, a partir de hoy, te permites vivir con mayor libertad de estas cargas?'
        ]
      }
    ]
  }
];
