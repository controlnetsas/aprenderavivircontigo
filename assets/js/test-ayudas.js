// Aprender a Vivir Contigo — Ayudas visuales de los tests: un dibujo pequeño y un ejemplo por cada pregunta,
// para poner al consultante en contexto. Se usa en el test paso a paso (reco-quiz.js) y en el de Genealogía.
// Los ejemplos son situaciones cotidianas para orientar; no son un diagnóstico.
(function () {
  'use strict';
  var ICONOS = {"heart": "<path d=\"M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z\"/>", "eye": "<path d=\"M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z\"/><circle cx=\"12\" cy=\"12\" r=\"3\"/>", "eye-off": "<path d=\"M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24\"/><line x1=\"1\" y1=\"1\" x2=\"23\" y2=\"23\"/>", "lock": "<rect x=\"3\" y=\"11\" width=\"18\" height=\"11\" rx=\"2\"/><path d=\"M7 11V7a5 5 0 0 1 10 0v4\"/>", "home": "<path d=\"M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z\"/><polyline points=\"9 22 9 12 15 12 15 22\"/>", "shield": "<path d=\"M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z\"/>", "star": "<polygon points=\"12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2\"/>", "sun": "<circle cx=\"12\" cy=\"12\" r=\"5\"/><line x1=\"12\" y1=\"1\" x2=\"12\" y2=\"3\"/><line x1=\"12\" y1=\"21\" x2=\"12\" y2=\"23\"/><line x1=\"4.22\" y1=\"4.22\" x2=\"5.64\" y2=\"5.64\"/><line x1=\"18.36\" y1=\"18.36\" x2=\"19.78\" y2=\"19.78\"/><line x1=\"1\" y1=\"12\" x2=\"3\" y2=\"12\"/><line x1=\"21\" y1=\"12\" x2=\"23\" y2=\"12\"/><line x1=\"4.22\" y1=\"19.78\" x2=\"5.64\" y2=\"18.36\"/><line x1=\"18.36\" y1=\"5.64\" x2=\"19.78\" y2=\"4.22\"/>", "moon": "<path d=\"M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z\"/>", "cloud": "<path d=\"M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z\"/>", "compass": "<circle cx=\"12\" cy=\"12\" r=\"10\"/><polygon points=\"16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76\"/>", "clock": "<circle cx=\"12\" cy=\"12\" r=\"10\"/><polyline points=\"12 6 12 12 16 14\"/>", "gift": "<polyline points=\"20 12 20 22 4 22 4 12\"/><rect x=\"2\" y=\"7\" width=\"20\" height=\"5\"/><line x1=\"12\" y1=\"22\" x2=\"12\" y2=\"7\"/><path d=\"M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z\"/><path d=\"M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z\"/>", "users": "<path d=\"M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2\"/><circle cx=\"9\" cy=\"7\" r=\"4\"/><path d=\"M23 21v-2a4 4 0 0 0-3-3.87\"/><path d=\"M16 3.13a4 4 0 0 1 0 7.75\"/>", "user": "<path d=\"M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\"/><circle cx=\"12\" cy=\"7\" r=\"4\"/>", "award": "<circle cx=\"12\" cy=\"8\" r=\"7\"/><polyline points=\"8.21 13.89 7 23 12 20 17 23 15.79 13.88\"/>", "check-square": "<polyline points=\"9 11 12 14 22 4\"/><path d=\"M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11\"/>", "book-open": "<path d=\"M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z\"/><path d=\"M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z\"/>", "search": "<circle cx=\"11\" cy=\"11\" r=\"8\"/><line x1=\"21\" y1=\"21\" x2=\"16.65\" y2=\"16.65\"/>", "link": "<path d=\"M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71\"/><path d=\"M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71\"/>", "zap": "<polygon points=\"13 2 3 14 12 14 11 22 21 10 12 10 13 2\"/>", "anchor": "<circle cx=\"12\" cy=\"5\" r=\"3\"/><line x1=\"12\" y1=\"22\" x2=\"12\" y2=\"8\"/><path d=\"M5 12H2a10 10 0 0 0 20 0h-3\"/>", "flag": "<path d=\"M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z\"/><line x1=\"4\" y1=\"22\" x2=\"4\" y2=\"15\"/>", "umbrella": "<path d=\"M23 12a11.05 11.05 0 0 0-22 0zm-5 7a3 3 0 0 1-6 0v-7\"/>", "message-circle": "<path d=\"M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z\"/>", "frown": "<circle cx=\"12\" cy=\"12\" r=\"10\"/><path d=\"M16 16s-1.5-2-4-2-4 2-4 2\"/><line x1=\"9\" y1=\"9\" x2=\"9.01\" y2=\"9\"/><line x1=\"15\" y1=\"9\" x2=\"15.01\" y2=\"9\"/>", "trending-up": "<polyline points=\"23 6 13.5 15.5 8.5 10.5 1 18\"/><polyline points=\"17 6 23 6 23 12\"/>", "layers": "<polygon points=\"12 2 2 7 12 12 22 7 12 2\"/><polyline points=\"2 17 12 22 22 17\"/><polyline points=\"2 12 12 17 22 12\"/>", "repeat": "<polyline points=\"17 1 21 5 17 9\"/><path d=\"M3 11V9a4 4 0 0 1 4-4h14\"/><polyline points=\"7 23 3 19 7 15\"/><path d=\"M21 13v2a4 4 0 0 1-4 4H3\"/>", "minimize": "<polyline points=\"4 14 10 14 10 20\"/><polyline points=\"20 10 14 10 14 4\"/><line x1=\"14\" y1=\"10\" x2=\"21\" y2=\"3\"/><line x1=\"3\" y1=\"21\" x2=\"10\" y2=\"14\"/>", "activity": "<polyline points=\"22 12 18 12 15 21 9 3 6 12 2 12\"/>", "feather": "<path d=\"M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z\"/><line x1=\"16\" y1=\"8\" x2=\"2\" y2=\"22\"/><line x1=\"17.5\" y1=\"15\" x2=\"9\" y2=\"15\"/>", "life-buoy": "<circle cx=\"12\" cy=\"12\" r=\"10\"/><circle cx=\"12\" cy=\"12\" r=\"4\"/><line x1=\"4.93\" y1=\"4.93\" x2=\"9.17\" y2=\"9.17\"/><line x1=\"14.83\" y1=\"14.83\" x2=\"19.07\" y2=\"19.07\"/><line x1=\"14.83\" y1=\"9.17\" x2=\"19.07\" y2=\"4.93\"/><line x1=\"4.93\" y1=\"19.07\" x2=\"9.17\" y2=\"14.83\"/>", "git-branch": "<line x1=\"6\" y1=\"3\" x2=\"6\" y2=\"15\"/><circle cx=\"18\" cy=\"6\" r=\"3\"/><circle cx=\"6\" cy=\"18\" r=\"3\"/><path d=\"M18 9a9 9 0 0 1-9 9\"/>", "package": "<path d=\"M16.5 9.4l-9-5.19M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z\"/><polyline points=\"3.27 6.96 12 12.01 20.73 6.96\"/><line x1=\"12\" y1=\"22.08\" x2=\"12\" y2=\"12\"/>", "mic-off": "<line x1=\"1\" y1=\"1\" x2=\"23\" y2=\"23\"/><path d=\"M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6\"/><path d=\"M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23\"/><line x1=\"12\" y1=\"19\" x2=\"12\" y2=\"23\"/><line x1=\"8\" y1=\"23\" x2=\"16\" y2=\"23\"/>"};
  var AYUDAS = {
"en-0-0": [
"eye",
"Terminas un trabajo que quedó bien y, aun así, piensas: «le faltó pulir esto»."
],
"en-0-1": [
"user",
"Al final del día notas que llevas los dientes apretados o los hombros hacia arriba, y recién ahí te das cuenta."
],
"en-0-2": [
"check-square",
"Prefieres lavar tú los platos porque «nadie los deja como debe ser»."
],
"en-1-0": [
"gift",
"Le llevas comida o ayuda a alguien sin que te la pida, y por dentro esperas que luego se acuerde de ti."
],
"en-1-1": [
"users",
"Te preguntan «¿tú qué quieres?» y respondes «lo que ustedes prefieran», o piensas primero en lo que le conviene al otro."
],
"en-1-2": [
"frown",
"Pasas la semana ayudando a una amiga y no llega ni un «gracias»; sonríes, pero te queda un nudo por dentro."
],
"en-2-0": [
"trending-up",
"Cierras una venta y te sientes invencible; si pierdes una, el día entero se te apaga."
],
"en-2-1": [
"layers",
"Con tu jefe hablas de una manera, con tus amigos de otra, y no siempre sabes cuál de las dos eres tú."
],
"en-2-2": [
"flag",
"Terminas un proyecto y, antes de celebrar, ya estás planeando el siguiente."
],
"en-3-0": [
"moon",
"Estás en una buena cena con gente querida y, aun así, sientes que algo no encaja del todo."
],
"en-3-1": [
"compass",
"Sueñas con la vida en otro lugar o con una relación del pasado, y lo que tienes hoy te parece gris."
],
"en-3-2": [
"cloud",
"Vives un momento muy feliz y de pronto te asalta una tristeza suave, sin un motivo claro."
],
"en-4-0": [
"lock",
"Después de una reunión larga con mucha gente necesitas encerrarte solo/a y apagar el teléfono."
],
"en-4-1": [
"life-buoy",
"Se te daña el carro o te enfermas y prefieres resolverlo solo/a antes que llamar a alguien."
],
"en-4-2": [
"search",
"En una discusión familiar te quedas observando y analizando en silencio, en vez de meterte."
],
"en-5-0": [
"umbrella",
"Tu pareja se demora en contestar y tu mente ya imaginó tres cosas que pudieron salir mal."
],
"en-5-1": [
"anchor",
"Antes de aceptar un trato pides referencias, contrato y una segunda opinión, aunque la persona parezca honesta."
],
"en-5-2": [
"shield",
"Te invitan a probar algo nuevo y lo primero que piensas son los riesgos, no lo emocionante."
],
"en-6-0": [
"zap",
"Una conversación te incomoda y propones de inmediato otro plan para cambiar de ambiente."
],
"en-6-1": [
"git-branch",
"Tienes tres cursos empezados, dos proyectos nuevos y un viaje en planeación, y apenas alcanzas con todo."
],
"en-6-2": [
"link",
"Te piden comprometerte con un solo proyecto durante un año y sientes que algo se cierra."
],
"en-7-0": [
"life-buoy",
"Llevas días desbordado/a de trabajo, pero cuando te ofrecen ayuda respondes «tranquilo/a, yo puedo»."
],
"en-7-1": [
"shield",
"Cuando algo se pone incierto, tomas el mando de la situación aunque nadie te lo haya pedido."
],
"en-7-2": [
"heart",
"Aunque quieras mucho a alguien, evitas llorar delante de esa persona o decirle «hoy me siento frágil»."
],
"en-8-0": [
"clock",
"Tienes algo importante por hacer y, en vez, ordenas el cajón, respondes correos viejos y lavas el carro."
],
"en-8-1": [
"compass",
"Te preguntan a dónde quieres ir a cenar y no sabes: te adaptas a lo que elijan los demás."
],
"en-8-2": [
"mic-off",
"En una reunión piensas distinto al grupo, pero dices «a mí me parece bien lo que decidan»."
],
"he-0-0": [
"minimize",
"Entras a una sala llena y te pegas a la pared, con los brazos cruzados, buscando pasar desapercibido/a."
],
"he-0-1": [
"eye-off",
"Alguien te mira con gesto serio, te quedas callado/a y te vas del lugar en cuanto puedes."
],
"he-0-2": [
"award",
"Revisas un texto diez veces antes de enviarlo porque temes que alguien encuentre un error."
],
"he-1-0": [
"users",
"Para elegir un trabajo o mudarte, llamas a varias personas antes de decidir."
],
"he-1-1": [
"message-circle",
"Una amiga te dice «hoy no podemos vernos» y pasas el día pensando si hiciste algo mal."
],
"he-1-2": [
"anchor",
"Al estar de pie o sentado/a te recuestas en algo —la pared, la silla, la persona de al lado— como buscando apoyo."
],
"he-2-0": [
"frown",
"Se rompe un plato en casa y, aunque no lo tocaste, piensas «seguro fue mi culpa»."
],
"he-2-1": [
"flag",
"Quieres decir «hoy no puedo» y sientes que estás pidiendo demasiado."
],
"he-2-2": [
"users",
"Evitas contar algo en público por miedo a que alguien se sienta incómodo o avergonzado."
],
"he-3-0": [
"link",
"Prefieres hacer tú todo el informe antes que arriesgarte a que otra persona lo entregue distinto."
],
"he-3-1": [
"check-square",
"Le encargas una tarea a alguien, revisas cada paso y te molesta todo lo que no sigue tu forma."
],
"he-3-2": [
"zap",
"Alguien prometió llamarte y no lo hizo: sientes una rabia que te desborda, aunque fuera un detalle."
],
"he-4-0": [
"cloud",
"Cuando una conversación se vuelve muy emotiva, te pones práctico/a o cambias de tema."
],
"he-4-1": [
"trending-up",
"Ves a un colega destacar y piensas «yo debería estar a su nivel», o te dices «al menos no soy el/la peor»."
],
"he-4-2": [
"gift",
"Te hacen un regalo o un elogio y respondes «no era necesario» o lo minimizas."
],
"vi-0-0": [
"award",
"Sacabas buenas notas o lograbas algo y sentías que a tu mamá siempre le parecía poco."
],
"vi-0-1": [
"gift",
"Una amiga te ofrece cuidarte cuando estás enferma/o y respondes «no te preocupes, yo puedo solo/a»."
],
"vi-0-2": [
"zap",
"Hay algo que te dolió de tu mamá y nunca se lo has dicho; lo recuerdas con rabia, en silencio."
],
"vi-0-3": [
"star",
"Cuando alguien menciona un defecto de tu mamá, la defiendes de inmediato: «ella hizo lo que pudo»."
],
"vi-0-4": [
"users",
"De niño o niña escuchabas sus penas, la consolabas o la cuidabas tú a ella."
],
"vi-0-5": [
"lock",
"Estás mal, pero le dices a todos «estoy bien» porque mostrarte frágil te incomoda."
],
"vi-0-6": [
"repeat",
"Te sorprendes diciéndole a tu hijo o a tu pareja la misma frase que tu mamá te decía a ti."
],
"vi-0-7": [
"cloud",
"Hablas con ella y todo es correcto, pero sientes una distancia que no sabes explicar."
],
"vi-0-8": [
"anchor",
"Aunque todo marche bien, vives con la sensación de que «en cualquier momento se cae todo»."
],
"vi-0-9": [
"frown",
"Dejas de llamarla unos días para cuidar tu paz y te sientes culpable."
],
"vi-0-10": [
"search",
"Esperas que tu pareja te cuide y te consuele como no lo hizo tu mamá."
],
"vi-0-11": [
"home",
"Te imaginas siendo madre o padre y piensas «¿y si termino siendo como ella?»."
],
"vi-0-12": [
"package",
"Sientes una tristeza que no parece tuya y descubres que se parece a la que cargaba tu mamá."
],
"vi-0-13": [
"mic-off",
"Ella insiste en algo que no quieres hacer y terminas diciendo que sí para evitar el conflicto."
],
"vi-0-14": [
"eye",
"Aunque ya eres adulto/a, sigues esperando que ella te diga «estoy orgullosa de ti»."
],
"vi-1-0": [
"award",
"Le mostrabas un logro a tu papá y sentías que nunca llegaba a ser suficiente."
],
"vi-1-1": [
"users",
"Un jefe te hace una corrección y la sientes como un ataque, o te cuesta aceptar un consejo."
],
"vi-1-2": [
"zap",
"Te duele algo que tu papá dijo o dejó de decir y nunca se lo has expresado."
],
"vi-1-3": [
"star",
"Siempre cuentas lo bueno de tu papá y se te hace difícil mencionar lo que te lastimó."
],
"vi-1-4": [
"home",
"Cuando tu papá faltaba, asumiste cuidar a tus hermanos o decidir cosas de la casa siendo aún niño o niña."
],
"vi-1-5": [
"compass",
"Antes de elegir una carrera o un trabajo, necesitas que varias personas te digan que es buena idea."
],
"vi-1-6": [
"repeat",
"Reaccionas con el mismo tono de voz o el mismo gesto que tu papá usaba y que tanto te molestaba."
],
"vi-1-7": [
"cloud",
"Estás con él en la mesa, pero sientes que en realidad no se conocen."
],
"vi-1-8": [
"flag",
"Te ascienden o te va muy bien y te asalta la sensación de «no me lo merezco»."
],
"vi-1-9": [
"frown",
"Te alejas de él para cuidar tu paz y después sientes que le estás fallando."
],
"vi-1-10": [
"shield",
"Tiendes a buscar parejas o jefes que te protejan o te den la aprobación que no tuviste de tu papá."
],
"vi-1-11": [
"home",
"Piensas en tener hijos y te preocupa «¿y si repito lo que viví con mi papá?»."
],
"vi-1-12": [
"package",
"Sientes una rabia o una responsabilidad que no parece tuya y que ya cargaba tu papá."
],
"vi-1-13": [
"mic-off",
"Tu papá te pide algo y, aunque no puedes, te cuesta decirle «no»."
],
"vi-1-14": [
"eye",
"Aunque ya eres adulto/a, sigues esperando que tu papá te mire con orgullo."
],
"gp-0-0-0": [
"home",
"Tu mamá repite «tú llegaste cuando menos lo esperábamos», o en tu casa nunca se habló de cómo fue tu embarazo."
],
"gp-0-0-1": [
"link",
"Sientes que debes «compensar» a tus padres por lo difícil que fue tenerte: eres siempre el responsable, el que no da problemas."
],
"gp-0-0-2": [
"heart",
"Ya de adulto/a todavía sientes que tienes que ganarte tu lugar en la familia para que te acepten."
],
"gp-0-1-0": [
"feather",
"Una tía que perdió un bebé, o un aborto del que alguien habló alguna vez en voz baja."
],
"gp-0-1-1": [
"mic-off",
"Cuando sale el tema, alguien dice «eso ya pasó, no hablemos de eso» y todos cambian de conversación."
],
"gp-0-2-0": [
"lock",
"Un familiar fue maltratado o abusado y todos «saben», pero nadie lo dice en voz alta."
],
"gp-0-2-1": [
"layers",
"Una hija que de niña hacía de «esposa» de su papá, o un hijo que consolaba a su mamá como si fuera su pareja."
],
"gp-0-2-2": [
"heart",
"Sentir que tuviste que esconder con quién te relacionas, o quién eres, para que tu familia te aceptara."
],
"gp-1-0-0": [
"message-circle",
"Dices seguido «siempre me abandonan» o «nada me sale bien» sin saber de dónde viene esa frase."
],
"gp-1-0-1": [
"cloud",
"Un miedo a la pobreza, a las enfermedades o a quedarte solo/a que no corresponde a lo que tú has vivido."
],
"gp-1-0-2": [
"book-open",
"«El que no trabaja no come», «aquí no se llora», «la plata todo lo daña»."
],
"gp-1-0-3": [
"clock",
"Te angustias a los 40 años, justo la edad en que murió tu abuelo o en que algo grave le pasó a tu mamá."
],
"gp-1-0-4": [
"package",
"Una tristeza, un miedo o una culpa que sientes pesada y que no encaja con tu propia historia."
],
"gp-1-1-0": [
"repeat",
"Varias mujeres de tu familia que se separaron jóvenes, o varios hombres con la misma profesión o el mismo problema de salud."
],
"gp-1-1-1": [
"compass",
"Siempre terminas en trabajos o parejas parecidas, aunque te propongas cambiar."
],
"gp-1-1-2": [
"eye",
"Darte cuenta de que tu repetición puede ser una forma de lealtad inconsciente a alguien de tu familia."
],
"gp-2-0-0": [
"lock",
"Un tío del que nadie quiere hablar, o una época de la familia —como una mudanza o una ruina— que se evita nombrar."
],
"gp-2-0-1": [
"search",
"Un familiar asesinado, un suicidio o un hecho de violencia que solo se menciona en susurros."
],
"gp-2-0-2": [
"zap",
"Una enfermedad, una adicción o una rabia constante en alguien de la familia que parece «hablar» de lo que no se cuenta."
],
"gp-2-1-0": [
"sun",
"Encender una vela o escribir el nombre de esa persona o de ese hecho, solo para ti, y notar si algo se alivia."
],
"gp-2-1-1": [
"users",
"Pensar que, antes de contarle algo difícil a tu mamá o a tus hermanos, te gustaría contar con un terapeuta que te acompañe."
],
"gp-3-0-0": [
"user",
"Un tío que se fue y no se volvió a nombrar, un hijo fuera del matrimonio o alguien «desheredado» o rechazado por la familia."
],
"gp-3-0-1": [
"heart",
"Tu papá tuvo una novia o una esposa antes de tu mamá, y casi nadie lo menciona."
],
"gp-3-0-2": [
"users",
"Vives algo parecido a lo que vivió tu abuela o tu tío: la misma soledad, los mismos pleitos, la misma decepción."
],
"gp-3-0-3": [
"search",
"Llegar a pensar «esta tristeza se parece a la de mi abuela», aunque no estés del todo seguro/a."
],
"gp-3-0-4": [
"gift",
"Decir en silencio «esto es tuyo, te lo devuelvo con respeto, y yo sigo adelante con mi vida»."
],
"gp-3-1-0": [
"activity",
"Al decir la frase sientes calor en el pecho, sueltas un suspiro o se te afloja la garganta."
],
"gp-3-1-1": [
"trending-up",
"Después del ejercicio notas que la carga pesa menos o que ya no la sientes tan tuya."
],
"gp-4-0-0": [
"book-open",
"Tu abuelo no pudo estudiar y tuvo que trabajar desde niño; no tuvo a nadie que le enseñara otra forma de vivir."
],
"gp-4-0-1": [
"heart",
"Pasar de «no se lo perdono» a «hizo lo que pudo con lo que tenía»."
],
"gp-4-1-0": [
"user",
"Al decir la frase notas que los hombros bajan, respiras más hondo o te sientes más firme."
],
"gp-4-1-1": [
"compass",
"Dormir mejor, poner un límite sin culpa o dejar de repetir una reacción de siempre."
],
"gp-5-0-0": [
"eye",
"Descubrir un dato o una conexión de tu familia que antes no habías visto."
],
"gp-5-0-1": [
"sun",
"Pensar «gracias, aun con todo lo que pasaron, me dieron la vida»."
],
"gp-5-0-2": [
"flag",
"Sentir que puedes elegir tu propio camino sin cargar historias que no te corresponden."
]
};
  function svg(nombre, tam) {
    var cuerpo = ICONOS[nombre] || ICONOS.heart;
    return '<svg xmlns="http://www.w3.org/2000/svg" width="' + tam + '" height="' + tam + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' + cuerpo + '</svg>';
  }
  window.AVC_AYUDA = {
    existe: function (id) { return !!AYUDAS[id]; },
    icono: function (id, tam) { var a = AYUDAS[id]; return a ? '<span class="avc-ico">' + svg(a[0], tam || 40) + '</span>' : ''; },
    ejemplo: function (id) {
      var a = AYUDAS[id];
      return a ? '<div class="avc-ejemplo"><span class="avc-ejemplo-etq">Por ejemplo</span><p>' + a[1] + '</p></div>' : '';
    }
  };
})();
