// Aprender a Vivir Contigo — Contenido del Taller de Genealogía "Sanando con mi Linaje"
// (guía de facilitación, versión solo explicaciones: objetivo, acuerdos, construcción del árbol,
// los seis temas con sus ejercicios [Objetivo + Procedimiento] y las Frases Sanadoras completas).
//
// Las preguntas de autoindagación de cada tema NO están aquí — viven, como cuestionario privado
// de autoindagación, en assets/js/genealogia-test.js / test-genealogia.html.

window.GENEALOGIA_TALLER = {
  objetivoGeneral: 'Este taller integra dos herramientas complementarias: la construcción del árbol genealógico —el mapa visual de tu sistema familiar— y un recorrido por seis temas sistémicos que te ayudan a leer ese mapa: reconocer lealtades invisibles, cargas transgeneracionales y secretos que puedan estar condicionando tu vida, para vivir con mayor libertad y conciencia.',
  comoUsarGuia: 'Primero construyes tu árbol (Parte I). Ese árbol se convierte en tu documento de trabajo: en la Parte II vuelves a él una y otra vez, en lugar de dibujar uno nuevo en cada tema.',

  acuerdos: [
    { titulo: 'Confidencialidad', texto: 'Lo que se comparte en el taller permanece en el taller.' },
    { titulo: 'Ritmo propio', texto: 'Cada participante decide cuánto compartir; el silencio y el "paso" son respuestas válidas.' },
    { titulo: 'Sin diagnósticos ni juicios', texto: 'Se explora sin etiquetar ni interpretar la vida de nadie.' },
    { titulo: 'Cuidado profesional', texto: 'Abuso, incesto, pérdidas u otras vivencias de alto impacto se introducen solo a nivel de toma de conciencia; si se activa una herida profunda, se deriva a acompañamiento terapéutico especializado.' },
    { titulo: 'Consentimiento activo', texto: 'Ningún ejercicio corporal o de contacto se realiza sin acuerdo explícito de todas las personas involucradas.' }
  ],

  parteI: {
    intro: 'El árbol genealógico es el mapa visual de tu sistema familiar. En el taller lo construyes con calma, guiado paso a paso: es la herramienta que usas a lo largo de todo el recorrido.',
    logica: 'Empiezas por ti, subes a tus padres, y desde ahí el árbol se divide en dos líneas —paterna y materna— que suben cada una por su lado hasta encontrarse solo en la generación de los padres. Se completa primero la línea paterna, luego la materna, y al final se unen en la hoja del diagrama completo, hasta llegar —si hay información— a bisabuelos y tatarabuelos.',
    casoEspecial: 'Caso especial — una persona con dos roles: si alguien ocupa dos posiciones (por ejemplo, es padre y abuelo a la vez), se repite el mismo símbolo en ambos lugares, conectados con una línea punteada. Esto reconoce la situación sin juzgarla.',
    simbolos: [
      { simbolo: '□', significado: 'Hombre' },
      { simbolo: '○', significado: 'Mujer' },
      { simbolo: '──────', significado: 'Matrimonio / convivencia (línea continua entre símbolos)' },
      { simbolo: '┄┄┄┄┄┄', significado: 'Convivencia sin matrimonio (línea punteada)' },
      { simbolo: '──╫──', significado: 'Divorcio (línea continua con dos rayitas cruzadas)' },
      { simbolo: '✕', significado: 'Fallecimiento (cruz sobre o junto al símbolo)' },
      { simbolo: '→', significado: 'Vínculo o lealtad emocional (flecha entre dos personas)' }
    ],
    recoleccion: 'Antes de completar cada rama, se recogen los datos de cada familiar en una pequeña ficha: nacimiento, matrimonio(s), divorcio, fallecimiento, migración o desplazamiento, situación de salud relevante y otros eventos significativos. Si no se conoce un dato, se deja en blanco a propósito — ese vacío también es información, y en el taller se trabaja con él.'
  },

  temas: [
    {
      numero: 1,
      titulo: 'La Sexualidad y el Embarazo',
      intro: 'La sexualidad no es solo un hecho biológico: es un canal por el que la vida y la historia familiar fluyen de una generación a otra. El embarazo es la expresión máxima de ese flujo, y en él se entrelazan el amor de los padres con las cargas no resueltas del sistema.',
      ejercicios: [
        { titulo: 'El relato de mi llegada', objetivo: 'Explorar cómo fue recibido tu embarazo y nacimiento en el sistema familiar.', procedimiento: [
          'Indaga o recuerda cómo describieron tus padres el momento en que supieron del embarazo.',
          'Escribe, en primera persona, una carta breve dirigida a tus padres, como si fueras ese bebé en el vientre.',
          'Comparte en díadas —o solo en el papel— lo que emergió.'
        ]},
        { titulo: 'Honrar lo no nacido', objetivo: 'Dar un lugar simbólico a embarazos interrumpidos, pérdidas o hijos no reconocidos —propios o de tus ancestros— que veas en tu árbol.', procedimiento: [
          'Revisa tu árbol: ¿hay algún embarazo que no llegó a término, o nunca se mencionó?',
          'Escribe el nombre —si se conoce— o una frase que le dé un lugar: «Perteneces a esta familia. Te veo.»',
          'Opcional: enciende una vela mientras lees la frase en voz alta.'
        ]}
      ],
      temasConsulta: {
        intro: 'Se presentan solo como puerta de entrada a la toma de conciencia; ninguno debe profundizarse sin acompañamiento profesional especializado.',
        items: [
          { titulo: 'Abuso e incesto', texto: 'Se abordan sin juzgar a nadie, reconociendo el impacto sistémico en todos los implicados —incluida la persona dañada— sin minimizar jamás la responsabilidad ni el daño. El incesto, además, rompe los límites y jerarquías del sistema (padres en su lugar de dar, hijos en su lugar de recibir).' },
          { titulo: 'Orientación sexual e identidad de género', texto: 'Bert Hellinger propuso hipótesis sobre posibles vínculos entre estos temas y dinámicas familiares no resueltas; esa lectura es debatida, y hoy la psicología contemporánea entiende ambos como expresiones legítimas de la identidad, no síntomas a resolver. Aquí el objetivo no es "explicar" ni "corregir" a nadie, sino observar si su lugar ha sido honrado tal como es.' }
        ]
      },
      notaFacilitador: 'Este tema puede movilizar recuerdos intensos sobre la propia concepción, embarazos, pérdidas gestacionales o vivencias de abuso. El facilitador sostiene un espacio pausado, con consentimiento explícito, y tiene previsto un canal de derivación terapéutica si algo activa una herida profunda.',
      fraseCierre: 'Cuando se honra el vínculo que nos dio la vida, se libera el flujo que permite vivir con mayor plenitud.'
    },
    {
      numero: 2,
      titulo: 'Conflictos Intergeneracionales y su Impacto',
      intro: 'Los conflictos que una generación no resuelve no desaparecen: buscan resolución, de manera inconsciente, a través de quienes vienen después. Reconocer el origen de un patrón que se repite —en la pareja, la salud, el dinero o las emociones— es el primer paso para dejar de repetirlo sin saberlo.',
      subseccion: {
        titulo: 'Por qué se repiten: epigenética y lenguaje nuclear',
        texto: 'La investigación científica ofrece dos explicaciones complementarias (basadas en el trabajo de Mark Wolynn, "Este dolor no es mío"). La epigenética muestra que el estrés y el trauma activan marcas químicas que regulan genes y pueden heredarse, sin cambiar el ADN mismo: estudios con hijos de sobrevivientes de eventos traumáticos muestran huellas biológicas compartidas, aunque ellos nunca vivieron el evento original. El lenguaje nuclear, por su parte, son palabras o frases cargadas emocionalmente que una persona repite sin saber bien por qué —pistas de un trauma heredado y no propio.'
      },
      ejercicios: [
        { titulo: 'Patrones repetitivos en tu árbol', objetivo: 'Con el árbol ya construido, identificar qué se repite entre generaciones.', procedimiento: [
          'Mira tu árbol completo con calma.',
          'Marca con un color las enfermedades, separaciones, dificultades económicas o patrones de pareja que veas repetirse en más de una rama.'
        ]}
      ],
      fraseCierre: 'Cuando reconocemos el origen de los conflictos, damos el primer paso hacia su liberación.'
    },
    {
      numero: 3,
      titulo: 'Herencia Transgeneracional y Secretos Familiares',
      intro: 'Los secretos no desaparecen por no nombrarse: generan un vacío en la conciencia del sistema familiar que las siguientes generaciones, sin saberlo, intentan llenar a través de síntomas, comportamientos o enfermedades. Reconocerlos —sin necesidad de exponerlos públicamente— es un acto profundamente liberador.',
      ejercicios: [
        { titulo: 'Detective de silencios familiares', objetivo: 'Identificar posibles secretos o temas evitados dentro del sistema.', procedimiento: [
          'Nota qué temas generan incomodidad, cambios de tema o silencio absoluto en tu familia.',
          'Revisa tu árbol: ¿hay algún símbolo que dejaste vacío por falta de información? Ese vacío también habla.'
        ]},
        { titulo: 'Ritual simbólico de reconocimiento', objetivo: 'Dar un espacio interno a un secreto identificado, para favorecer el equilibrio del sistema.', procedimiento: [
          'Escribe el secreto —o tu intuición sobre él— en un papel, sin mostrarlo a nadie.',
          'Sostenlo en silencio unos minutos, reconociendo que forma parte de la historia familiar.',
          'Decide, sin presión, si el papel se guarda, se destruye simbólicamente o se conserva.'
        ]}
      ],
      fraseCierre: 'Lo que no se dice, se repite en el cuerpo o en la vida de alguien más.'
    },
    {
      numero: 4,
      titulo: 'Cargas y Lealtades Transgeneracionales',
      intro: 'Las lealtades invisibles llevan, sin decidirlo conscientemente, a repetir el destino de un antepasado como una forma inconsciente de pertenecer o de "aliviar" su sufrimiento. Distinguir "esto es mío" de "esto es de mi linaje" es el corazón de este tema.',
      ejercicios: [
        { titulo: 'Exclusiones y lealtades en tu árbol', objetivo: 'Revisar tu árbol para identificar a quién falta reconocer, y qué carga podrías estar sosteniendo por lealtad invisible.', procedimiento: [
          'Revisa tu árbol: ¿falta alguien que sabes que existió y no colocaste? ¿Hay una historia "que no se habla" detrás de algún vacío?',
          'Compara tu vida actual con la de tus ancestros: ¿qué destino o patrón de sufrimiento —económico, de salud, de pareja— ves repetirse?'
        ]},
        { titulo: 'Frase sistémica de devolución', objetivo: 'Practicar una frase propia del trabajo sistémico para soltar una lealtad invisible identificada.', procedimiento: [
          'Coloca una silla u objeto que represente al ancestro identificado.',
          'De pie frente a él, di en voz alta: «Yo te veo. Honro tu destino. Yo soy pequeño y tú grande. Tomo la vida que me diste, y dejo tu carga donde pertenece: contigo.»'
        ]}
      ],
      fraseCierre: 'El amor ciego dice: "yo también". El amor consciente dice: "yo te veo y te honro".'
    },
    {
      numero: 5,
      titulo: 'Liberación de Patrones Familiares',
      intro: 'Liberarse no es rechazar el pasado, sino integrarlo desde el amor y el respeto, reconociendo que los ancestros hicieron lo mejor que pudieron con los recursos que tenían.',
      ejercicios: [
        { titulo: 'Carta de comprensión a un ancestro', objetivo: 'Practicar la mirada compasiva hacia un ancestro con quien existe conflicto o distancia emocional.', procedimiento: [
          'Elige, de tu árbol, un ancestro con quien sientas conflicto o distancia.',
          'Escribe una carta reconociendo el contexto y las limitaciones que probablemente enfrentó, sin justificar ningún daño causado.'
        ]},
        { titulo: 'Ritual de integración y compromiso', objetivo: 'Marcar simbólicamente el acto de liberación y transformarlo en un paso concreto.', procedimiento: [
          'De pie, frente a tu árbol, di en voz alta: «Honro de dónde vengo. Tomo lo que me sirve, dejo lo que no me pertenece, y sigo mi propio camino con amor.»',
          'Escribe un compromiso concreto que refleje "vivir tu propio destino" en las próximas semanas.'
        ]}
      ],
      fraseCierre: 'Solo cuando honramos el pasado, podemos liberarnos de él.'
    },
    {
      numero: 6,
      titulo: 'El Flujo de la Vida — Cierre Integrador',
      intro: 'Cuando cada persona ocupa su lugar, se honra lo que fue y se equilibra el dar y el recibir, el amor puede fluir con mayor libertad a través de las generaciones.',
      ejercicios: [
        { titulo: 'Círculo del flujo de la vida', objetivo: 'Cerrar el taller integrando los aprendizajes de todo el recorrido.', procedimiento: [
          'Si el taller es grupal, se forma un círculo; si es individual, te sientas con tu árbol y tus notas de todo el taller a la vista.',
          'Completas, por escrito o en voz alta, tres frases: «Reconozco...», «Honro...», «Me permito...»'
        ]}
      ],
      fraseCierre: 'La vida fluye a través de nosotros cuando nos permitimos estar en nuestro lugar, honrando lo que fue, aceptando lo que es y permitiendo lo que será.'
    }
  ],

  cierre: {
    intro: 'Este recorrido no termina aquí: es el inicio de una mirada más consciente hacia tu historia familiar. Antes de cerrar, vale la pena reconocer el camino recorrido.',
    logros: [
      'Construimos tu árbol genealógico, dando un lugar visible a varias generaciones de tu sistema familiar.',
      'Exploramos cómo la sexualidad y el embarazo llevan consigo lealtades y cargas heredadas, y aprendiste a mirarlas sin juicio.',
      'Identificaste patrones intergeneracionales repetitivos y comprendiste, a través de la epigenética y el lenguaje nuclear, por qué se transmiten.',
      'Diste un lugar consciente a secretos familiares que antes permanecían en silencio, iniciando su liberación.',
      'Reconociste lealtades invisibles y cargas que no te pertenecen, y practicaste el gesto de devolverlas con respeto.',
      'Comenzaste a integrar el pasado desde el amor, soltando patrones que ya no necesitas repetir.',
      'Cerraste el recorrido honrando el flujo de la vida y el compromiso de vivir con mayor libertad y conciencia.'
    ],
    nota: 'El árbol genealógico y lo que reconociste en cada tema son herramientas vivas: puedes seguir revisándolos con el tiempo, a medida que recuerdas o te comparten nuevos detalles de tu historia familiar.'
  },

  frasesSanadoras: {
    intro: 'Como cierre y complemento de este taller, se incluye una selección de frases sanadoras enfocadas en la genealogía, las lealtades familiares y los excluidos del sistema — el mismo territorio que trabajaste al construir tu árbol. Estas frases no se recitan de forma mecánica: son un modelo que cada participante puede reescribir con sus propias palabras y su caso concreto.',
    contexto: 'Cada persona lleva, sin saberlo, una "contabilidad" de lo dado y recibido en su familia de origen — lo que Iván Boszormenyi-Nagy llamó lealtades invisibles. Cuando un miembro del sistema fue excluido, silenciado o nunca nombrado —un aborto, un hijo dado en adopción, un exiliado, un "deshonrado" de la familia—, su lugar vacío suele ser ocupado inconscientemente por un descendiente posterior. Estas frases trabajan para devolver a cada quien su lugar.',
    comoUsar: [
      'No se recitan de forma literal ni mecánica: son un modelo. Cada participante las reescribe con sus propias palabras y su caso concreto.',
      'Se leen despacio, en primera persona, en voz alta o interiorizadas en meditación.',
      'No sustituyen la psicoterapia; son un complemento de trabajo interior y toma de consciencia.',
      'Hay muchas más frases posibles que las incluidas aquí: esta guía selecciona las más representativas; la persona facilitadora puede ampliar con el cuaderno completo si lo necesita.'
    ],
    meditacion: {
      titulo: 'Recomendación de uso: meditación de diálogo imaginado',
      intro: 'La forma más profunda de trabajar cualquier frase de este taller es a través de una breve meditación en la que se imagina un diálogo con la persona o la situación implicada — en este caso, con tus antepasados o con quien haya sido excluido del sistema. No se trata de "pensar" la frase, sino de sentir que se le habla directamente a quien corresponde.',
      pasos: [
        'Busca un lugar tranquilo, siéntate cómodamente y, si te resulta fácil, cierra los ojos.',
        'Haz tres respiraciones lentas y profundas para bajar el ritmo antes de empezar.',
        'Imagina frente a ti, con la mayor presencia posible, a tus antepasados o a la persona excluida del sistema con quien vas a dialogar. No hace falta verla con detalle: basta con sentir que está ahí.',
        'Elige una frase de esta sección y dísela despacio, en voz alta o mentalmente, como si le hablaras directamente a esa presencia.',
        'Haz una pausa y observa qué sientes en el cuerpo antes de decir la siguiente frase o de continuar.',
        'Cierra el diálogo agradeciendo el encuentro, aunque haya sido solo imaginado, y respira profundamente para volver al presente.'
      ]
    },
    ambiente: [
      'Un lugar tranquilo, con música suave de fondo, en una postura sentada o, si no es posible, acostada.',
      'Los dos mejores momentos del día para practicarla son: al despertar, antes de empezar la jornada, y por la noche, justo antes de dormir — en ambos, la mente está más receptiva y las frases se integran con más facilidad en el inconsciente.'
    ],
    categorias: [
      { titulo: 'Para honrar a los antepasados', frases: [
        'Papá y mamá, abuelos y abuelas, bisabuelos y bisabuelas, y todos los que me antecedieron: mi vida procede de vosotros y de cómo se forjaron vuestros destinos.',
        'Me doy cuenta de que yo soy el pequeño o la pequeña a vuestro lado, y de que vosotros estáis mucho más preparados que yo para sostener vuestras propias heridas.',
        'Os llevo a todos en mi corazón; sois mis raíces, sois la fuente de la fuerza y el coraje que necesito para afrontar mi propio camino.'
      ]},
      { titulo: 'Para incluir a un excluido o a un yaciente del sistema', frases: [
        'Tú eres mi hermano o hermana mayor, o mi tío, o mi abuela: yo soy tu hermano menor, tu sobrina, tu nieto. Te doy un lugar en mi corazón.',
        'Yo no puedo vivir por ti ni ocupar tu lugar en la familia, porque si lo hago me hago daño y tú te quedas sin un lugar propio.',
        'Te honro a partir de ahora construyendo mi propia vida y dedicándote mis alegrías y mis éxitos.'
      ]},
      { titulo: 'Para honrar a las mujeres y a los hombres del clan', frases: [
        'Mamá, abuelas, tías, y todas las mujeres de mi clan: honro el dolor que padecisteis en vuestra condición de mujer según la época que os tocó vivir.',
        'Papá, abuelos, tíos, y todos los hombres de mi clan: honro a vuestros niños interiores heridos, especialmente cuando no os permitieron llorar.'
      ]},
      { titulo: 'Sobre el vínculo con quienes ya no están o nunca conocí', frases: [
        'Estoy vinculado también con mis antepasados a pesar de no haberlos conocido en vida, porque si sus circunstancias hubieran sido otras, probablemente yo no habría nacido.',
        'Puedo sanar y reconstruir mis vínculos a través del amor consciente, el amor que marca cuál es mi lugar ante mis seres queridos.'
      ]},
      { titulo: 'Para honrar el dolor del pasado familiar', frases: [
        'Honro las heridas de mi linaje que siguen abiertas, tanto las que conozco como las que todavía no, por la oportunidad que me ofrecen de seguir aprendiendo.',
        'Honrar el pasado familiar tal como fue evita que perpetúe la posición de víctima o de culpable ante lo sucedido.'
      ]},
      { titulo: 'Para aceptar el destino de mis ancestros', frases: [
        'Acepto que cada uno de mis ancestros vivió, amó y sufrió según lo que su época y sus circunstancias le permitieron.',
        'No me corresponde juzgar las decisiones de quienes me precedieron; solo reconocerlas como parte del camino que me trajo hasta aquí.',
        'Dejo a cada ancestro su destino completo, con lo bueno y lo difícil, y tomo de él solo la vida que me dio.'
      ]},
      { titulo: 'Cuando la vida de un padre o una madre ya se apagó', frases: [
        'Mamá (o papá), aunque ya no estás en este mundo, sigues teniendo un lugar en mi corazón y en mi vida.',
        'Te dejo partir con amor; tu muerte no borra todo lo que me diste, y lo llevo conmigo al vivir mi propia vida.',
        'Honro tu recuerdo viviendo plenamente, no deteniéndome en el dolor de tu ausencia.'
      ]},
      { titulo: 'Duelo por un hermano o una hermana', frases: [
        'Hermano, hermana: tu lugar en esta familia sigue siendo tuyo; yo no necesito ocuparlo ni compensarlo.',
        'Te recuerdo con amor y sigo mi camino, sabiendo que también tú querrías que yo viviera en plenitud.',
        'El vacío que dejaste tiene un lugar en mi corazón, sin que eso me impida seguir adelante.'
      ]},
      { titulo: 'Un hijo o un hermano que no llegó a nacer o partió muy pronto', frases: [
        'Pequeño mío (o hermano mío): tienes un lugar en esta familia, aunque tu tiempo aquí haya sido breve. Te veo y te reconozco.',
        'Tu vida, por corta que haya sido, tiene el mismo valor que cualquier otra; no te olvido ni te excluyo.',
        'Te doy un lugar en mi corazón y sigo viviendo, honrando también la vida que a mí sí me fue dada.'
      ]},
      { titulo: 'De un hijo adoptado hacia sus padres biológicos', frases: [
        'Ustedes me dieron la vida, y por las circunstancias que fueran, no pudieron criarme. Los honro como mi origen.',
        'Tomo de ustedes la vida que me dieron; el resto de mi crianza y mi historia se las entrego a quienes me acogieron.',
        'Aunque no crecí a su lado, sigo perteneciendo a su linaje, y eso nadie puede quitármelo.'
      ]},
      { titulo: 'De un hijo adoptado hacia sus padres de crianza', frases: [
        'Ustedes me dieron un hogar, cuidado y amor; los reconozco como mis padres en la vida cotidiana.',
        'Puedo honrar a mis padres biológicos y, al mismo tiempo, pertenecer plenamente a esta familia que me acogió.',
        'Gracias por darme un lugar; hoy tomo con gratitud todo lo que me han dado.'
      ]},
      { titulo: 'Cuando los padres se separan', frases: [
        'Mamá y papá: aunque su relación de pareja terminó, para mí ustedes siguen siendo, los dos, mi mamá y mi papá.',
        'No es mi tarea unirlos de nuevo ni tomar partido; mi lugar es el de hijo o hija, no el de árbitro de su relación.',
        'Los sigo amando a ambos por separado, y eso no me divide: me permite recibir el amor de los dos.'
      ]},
      { titulo: 'Cuando repito experiencias que vivieron mis padres', frases: [
        'Reconozco que algunos patrones de mi vida se parecen a los de mis padres; los veo con respeto, no con miedo.',
        'No necesito repetir su destino para sentirme parte de la familia; puedo pertenecer y, aun así, vivir diferente.',
        'Tomo lo que me sirve de su ejemplo y dejo, con amor, lo que no me pertenece vivir de nuevo.'
      ]},
      { titulo: 'Para independizarme de mis padres con amor', frases: [
        'Mamá, papá: gracias por la vida que me dieron. Hoy tomo mi propio camino, sin que eso signifique alejarme de ustedes en el corazón.',
        'Puedo construir mi vida, mis decisiones y mi hogar, siendo igual de su hijo o hija que siempre.',
        'Crecer y separarme de ustedes es también una forma de honrar todo lo que me enseñaron.'
      ]},
      { titulo: 'Para honrar a un ser querido que ya no está', frases: [
        'Te llevo en el recuerdo con cariño, y dejo que ese recuerdo me acompañe sin detener mi vida.',
        'Agradezco el tiempo que compartimos, y sigo adelante llevando contigo lo que me diste.',
        'Tu lugar en mi historia permanece intacto, y desde ahí te sigo honrando cada día.'
      ]}
    ]
  },

  referencias: [
    'Hellinger, B. (1999). La fuente no necesita preguntar por el camino: Constelaciones familiares y su significado para el individuo. Herder Editorial.',
    'Hellinger, B. (2001). Los órdenes del amor: Curso básico de constelaciones familiares. Herder Editorial.',
    'Hellinger, B. (2006). La paz comienza en el alma: Soluciones sistémicas para los conflictos del mundo. Herder Editorial.',
    'Wolynn, M. (2016). Este dolor no es mío: Identifica y sana los traumas familiares heredados. Ediciones Gaia.',
    'Fournillier Roa, P. A. (2024). Relaciones e Implicaciones Familiares — Módulo III. Formación Internacional en Constelaciones Familiares, CIL LATAM.',
    'Aprender a Vivir Contigo (s.f.). Taller Frases Sanadoras: un recorrido completo del vientre a la abundancia. Guía de facilitación, Módulo IV — Genealogía, lealtades y excluidos.'
  ]
};
