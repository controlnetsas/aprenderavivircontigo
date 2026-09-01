// Aprender a Vivir Contigo — Test del Eneatipo y de las Heridas de la Infancia.
//
// Igual que el Test de Genealogía: es un cuestionario guiado de autoindagación, NO se puntúa ni
// arroja un "resultado" ni un tipo fijo. Las respuestas se escriben y se quedan solo en el
// navegador de la persona (nunca se envían a ningún servidor) — al final se pueden descargar en
// un archivo de texto. El contenido de referencia completo de cada patrón (infancia, juventud,
// adultez, por qué se construyó, ejercicio de resignificación) vive en reconocete.js y se ve en
// detalle en reconocete-modulo-3.html; este archivo convierte ese contenido en preguntas de
// autoobservación para el test guiado de descubrete.html.

window.RECONOCETE_TEST_ENEATIPOS = [
  {
    tema: 'Eneatipo 1 · El Reformador',
    contexto: 'Tu mente busca constantemente el orden, la corrección y la coherencia. Vives con un fuerte sentido del deber y te exiges cumplir con lo que consideras "correcto", aunque eso implique postergar tu propio descanso.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sueles notar una voz interna que corrige o busca "hacerlo mejor", incluso cuando algo ya está bien?',
          '¿Sueles guardar la tensión de este patrón en el cuerpo —mandíbula, hombros, manos— sin darte cuenta?',
          '¿Te cuesta delegar algo por miedo a que "no quede bien hecho"?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Recuerdas algún momento de tu infancia en que el cariño o la aprobación parecían depender de portarte "correctamente" o no cometer errores?',
          '¿Qué se sentía más seguro entonces: señalar lo que estaba mal, o quedarte en silencio?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la serenidad',
        preguntas: [
          'Si algo que hiciste hoy "de forma imperfecta" ya fuera suficiente, ¿qué cambiaría en tu día?',
          '¿Qué necesitarías sentir para soltar un poco el control sin perder tu sentido de responsabilidad?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 2 · El Generoso',
    contexto: 'Das con calidez y facilidad, y sueles anticipar lo que otros necesitan antes de que lo pidan. Tu autoestima suele apoyarse en sentirte necesitado/a o indispensable para las personas que quieres.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sueles dar a otros sin que te lo pidan, esperando —aunque no lo digas— algo a cambio?',
          '¿Te resulta más fácil nombrar lo que necesitan los demás que reconocer lo que tú necesitas?',
          '¿Sientes una decepción silenciosa cuando ayudas mucho y no hay un reconocimiento explícito?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Recuerdas haber sentido, de niño o niña, que ser útil o servicial te acercaba al cariño de alguien ocupado o distante?',
          '¿En qué momento aprendiste que pedir ayuda podía sentirse como una carga para otros?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la humildad',
        preguntas: [
          '¿Cuál es una necesidad tuya de hoy que nadie más te ha ofrecido, sin que tengas que ganártela primero?',
          '¿A quién y con qué palabras concretas podrías pedirle algo esta semana, solo para ti?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 3 · El Triunfador',
    contexto: 'Te orientas al logro y sabes adaptarte para brillar frente a los demás. Tu capacidad de hacer que las cosas sucedan es notable, aunque a veces cuesta distinguir lo que sientes de lo que "se supone" que debes mostrar.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Tu ánimo sube o baja con fuerza según tus resultados o logros recientes?',
          '¿Sueles ajustar tu imagen según lo que sientes que el entorno espera de ti?',
          '¿Te cuesta detenerte a preguntarte qué sientes, más allá de la siguiente meta?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿El cariño o la atención que recibías de niño/a parecían depender de tus notas, logros o de "quedar bien" frente a otros?',
          '¿Recuerdas tener una versión de ti distinta con tu familia y otra con tus amigos?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la veracidad',
        preguntas: [
          '¿Qué sientes en este momento, sin que tenga que servir para nada ni demostrarle nada a nadie?',
          '¿En qué momento de esta semana podrías mostrar lo que realmente sientes, en vez de lo que "conviene" mostrar?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 4 · El Individualista',
    contexto: 'Sientes con intensidad y buscas la autenticidad en todo lo que haces. Aunque las cosas vayan bien, es común que sientas que "algo falta" o que hay una pieza importante ausente.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sientes con frecuencia que "algo falta", incluso cuando tienes cosas buenas presentes?',
          '¿Idealizas con frecuencia lo ausente o lo lejano, más que lo que ya tienes cerca?',
          '¿Te cuesta sostener la alegría de lo presente sin que aparezca la melancolía?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Hubo en tu infancia una pérdida, ausencia significativa o comparación constante con un hermano/a que marcó cómo aprendiste a sentir el amor?',
          '¿En la adolescencia cultivaste tu diferencia con los demás como parte de tu identidad, en vez de evitarla?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la ecuanimidad',
        preguntas: [
          'Elige algo simple que tienes ahora mismo cerca — ¿qué notas en él si lo observas sin compararlo con lo que falta?',
          '¿Qué significa para ti hoy la frase "lo que tengo ahora también es suficiente"?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 5 · El Investigador',
    contexto: 'Necesitas espacio y tiempo propio para "recargarte" después de estar con otros, y sueles observar y analizar una situación antes de involucrarte emocionalmente en ella.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sueles retirarte —físicamente o hacia adentro— cuando sientes que te piden más energía emocional de la que tienes disponible?',
          '¿Te cuesta pedir ayuda o mostrar necesidad frente a otras personas?',
          '¿Prefieres entender una situación desde la distancia antes que participar directamente en ella?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Sentiste, de niño/a, que conectar o pedir algo a un adulto cercano era abrumador, poco seguro o poco disponible?',
          '¿Tu mundo interior —libros, ideas, silencio— se volvió un refugio confiable frente al contacto emocional directo?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: el desapego sano',
        preguntas: [
          'Piensa en alguien de confianza: ¿qué necesitas hoy que podrías compartirle, aunque sea algo pequeño?',
          '¿Qué aparece en tu cuerpo —tensión, alivio, deseo de huir— al imaginar mostrarte un poco más cerca de alguien?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 6 · El Leal',
    contexto: 'Anticipas los riesgos antes de que aparezcan y valoras profundamente la seguridad y la lealtad en tus vínculos. Tu mente está entrenada para prepararse ante lo que podría salir mal.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Tu mente juega con frecuencia a "y si pasa esto", incluso en situaciones seguras?',
          '¿Te cuesta confiar del todo en los demás —o en ti mismo/a— sin buscar antes una garantía?',
          '¿Tu primera reacción frente a algo nuevo suele ser la cautela más que la curiosidad?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿El entorno en el que creciste transmitía, directa o sutilmente, que el mundo era impredecible o poco seguro?',
          '¿Dudabas de las figuras de autoridad en tu adolescencia, aunque al mismo tiempo buscabas su guía o aprobación?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la valentía de confiar',
        preguntas: [
          'Identifica tres momentos recientes en que tu mente se adelantó a un peligro que no llegó — ¿qué tienen en común?',
          '¿Qué necesitarías sentir para estar seguro/a incluso sin tener todas las garantías?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 7 · El Entusiasta',
    contexto: 'Buscas estímulo, opciones y mantener la vida ligera y placentera. Te cuesta quedarte con una emoción incómoda sin buscar distraerte o pasar rápidamente a otra cosa.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sueles pasar rápido a "la próxima cosa" cuando sientes una leve incomodidad, antes de terminar de sentirla?',
          '¿Sueles tener más planes o intereses abiertos de los que realmente puedes sostener?',
          '¿El compromiso profundo con una sola cosa te hace sentir que pierdes otras opciones?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Aprendiste, de niño/a, a "pasar rápido" del dolor o la incomodidad porque el entorno no tenía espacio para sostener tu malestar?',
          '¿Recuerdas un momento en que detenerte en lo difícil no se sintió seguro ni acompañado?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la sobriedad',
        preguntas: [
          'Recuerda un malestar reciente que evitaste sintiendo de lleno — ¿qué aparece en el cuerpo si te quedas con él dos minutos, sin arreglarlo?',
          '¿Qué te ayudaría a comprometerte con algo aunque aparezca la primera dificultad?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 8 · El Desafiador',
    contexto: 'Confrontas de frente y proteges con fuerza lo que consideras justo. Te cuesta mostrar debilidad o necesidad frente a otras personas, y sueles tomar el control casi automáticamente ante el desorden o la injusticia.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Sueles necesitar ayuda y aun así no pedirla?',
          '¿El control aparece como una forma automática de evitar sentirte expuesto/a?',
          '¿Te cuesta mostrar necesidad o debilidad, incluso con quienes más quieres?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Tuviste que volverte fuerte antes de tiempo —protegerte a ti mismo/a, o a otros— en un entorno donde mostrarse vulnerable no se sentía seguro?',
          '¿Preferías la confrontación directa a "andar con rodeos" desde joven?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la inocencia',
        preguntas: [
          'Piensa en una sola persona de confianza: ¿qué necesitas realmente de ella que podrías comunicarle, aunque sea en una frase breve?',
          '¿Qué se siente en el cuerpo al reconocer, sin juzgarte, que también puedes necesitar apoyo?'
        ]
      }
    ]
  },
  {
    tema: 'Eneatipo 9 · El Pacificador',
    contexto: 'Buscas la paz y te adaptas con facilidad a lo que otros necesitan, aunque a veces esto ocurre a costa de tu propia voz y tus propias prioridades.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Postergas lo importante ocupándote de tareas menores?',
          '¿Te cuesta identificar qué es lo que tú realmente quieres, más allá de lo que quieren los demás?',
          '¿Sueles evitar sostener tu propia opinión frente a un grupo?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Sentiste, de niño/a, que tu voz o tus necesidades quedaban en segundo plano frente a las de otros miembros de la familia?',
          '¿Adaptarte en silencio se volvió tu manera de mantener la paz?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: la acción sobre lo que importa',
        preguntas: [
          'Si nadie más opinara, ¿qué elegirías tú ahora mismo?',
          '¿Cuál es un asunto pequeño y postergado al que podrías dar un solo paso concreto esta semana?'
        ]
      }
    ]
  }
];

window.RECONOCETE_TEST_HERIDAS = [
  {
    tema: 'Herida del Rechazo · Máscara Huidiza',
    contexto: 'Aprendiste muy temprano a hacerte pequeño/a para protegerte del juicio o del rechazo, y hoy sigues sintiendo, con frecuencia, que ocupas "demasiado lugar".',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Tu cuerpo tiende a encogerse u ocupar el menor espacio posible sin que lo notes?',
          '¿Desapareces —física o emocionalmente— cuando sientes que podrías ser juzgado/a?',
          '¿La búsqueda de la perfección funciona como un escudo frente al juicio de otros?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Sentiste, en tus primeros años, que tu sola presencia era motivo de incomodidad para alguien importante en tu crianza?',
          '¿En la adolescencia preferías pasar inadvertido/a en las conversaciones grupales?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: ocupar tu lugar sin miedo',
        preguntas: [
          '¿Qué cambiaría si te permitieras, aunque sea una vez esta semana, quedarte presente en vez de desaparecer?',
          '¿Qué se siente en el cuerpo al repetir en silencio "tengo derecho a ocupar mi lugar"?'
        ]
      }
    ]
  },
  {
    tema: 'Herida del Abandono · Máscara Dependiente',
    contexto: 'La soledad y la ausencia dejaron una marca profunda en ti, y hoy te resulta especialmente difícil estar —o hacer planes— en soledad.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Te cuesta tomar decisiones importantes sin consultarlas antes con alguien más?',
          '¿Un simple "no" de alguien cercano te afecta más de lo que parece razonable?',
          '¿Sientes que tu cuerpo —especialmente la columna— "pide apoyo", como si necesitara sostenerse en algo externo?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Sentiste poca disponibilidad emocional de una figura importante en tu infancia —presente físicamente, pero ausente en la conexión?',
          '¿La tristeza aparecía desproporcionada cuando alguien se alejaba o terminaba un encuentro contigo?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: sostenerte a ti mismo/a',
        preguntas: [
          '¿Qué aparece en ti al imaginar estar en soledad intencional, sin distracciones ni compañía, por un rato breve?',
          '¿Qué se siente en el cuerpo al repetir "puedo sostenerme a mí mismo/a"?'
        ]
      }
    ]
  },
  {
    tema: 'Herida de la Humillación · Máscara Masoquista',
    contexto: 'Cargar con culpas y vergüenzas que no te pertenecen se volvió costumbre, y hoy te cuesta pedir lo que necesitas, incluso cuando es razonable hacerlo.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Te culpas con facilidad por situaciones que no dependían de ti?',
          '¿Pedir ayuda o expresar un límite te sigue pareciendo "excesivo"?',
          '¿Temes hacer sentir vergüenza a otros, incluso cuando no has hecho nada incorrecto?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Se habló de tu cuerpo, tu sexualidad o tus necesidades de una forma que te generó vergüenza cuando eras niño/a?',
          '¿Empezaste, en la adolescencia, a cargar con responsabilidades o culpas que en realidad no eran tuyas?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: pedir lo que necesitas',
        preguntas: [
          '¿Cuál es una necesidad pequeña y concreta que tienes hoy y que podrías nombrar en voz alta a alguien de confianza?',
          '¿Qué aparece en el cuerpo —calor, tensión, ganas de callar— al imaginar decirla?'
        ]
      }
    ]
  },
  {
    tema: 'Herida de la Traición · Máscara Controladora',
    contexto: 'Confiar y delegar te cuesta trabajo, porque el control se volvió, en algún momento, tu forma de protección.',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Te cuesta delegar o confiar en que otros harán las cosas tan bien como tú?',
          '¿Eres muy exigente con las personas en las que confías una responsabilidad?',
          '¿Reaccionas con intensidad cuando sientes que alguien no cumplió su palabra?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Sentiste una promesa o expectativa especial en tu infancia que después se sintió incumplida?',
          '¿Empezaste a asumir control o responsabilidad incluso cuando nadie te lo pedía, como forma de asegurar que las cosas salieran bien?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: soltar el control',
        preguntas: [
          '¿Qué tarea pequeña podrías delegar esta semana sin supervisar cada detalle?',
          '¿Qué se siente en el cuerpo al repetir "puedo confiar y seguir estando bien si algo no sale exactamente como espero"?'
        ]
      }
    ]
  },
  {
    tema: 'Herida de la Injusticia · Máscara Rígida',
    contexto: 'El control emocional y la exigencia contigo mismo/a se volvieron una forma de sobrevivir, y hoy te cuesta recibir un regalo, un elogio o una ayuda sin sentir que "es demasiado".',
    grupos: [
      {
        titulo: 'Reconocer el patrón hoy',
        preguntas: [
          '¿Te desconectas de lo que sientes justo cuando la emoción es más intensa?',
          '¿Sueles compararte con frecuencia con "el mejor" o con "el peor" para ubicarte?',
          '¿Recibir cariño, ayuda o reconocimiento se siente incómodo, como si no terminaras de merecerlo?'
        ]
      },
      {
        titulo: 'El origen en tu historia',
        preguntas: [
          '¿Sentiste poca calidez emocional de una figura importante en tu infancia —una relación fría, exigente o distante?',
          '¿Te volviste muy exigente contigo mismo/a en busca de hacer las cosas "de forma justa y exacta"?'
        ]
      },
      {
        titulo: 'Hacia tu antídoto: recibir sin miedo a "ser demasiado"',
        preguntas: [
          'Recuerda un elogio o gesto de cariño reciente que minimizaste — ¿qué pasaría si lo recibieras ahora, sin corregirlo?',
          '¿A quién podrías simplemente decirle "gracias" esta semana, sin restarle valor con palabras o pensamientos?'
        ]
      }
    ]
  }
];
