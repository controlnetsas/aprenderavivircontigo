<?php
// test-reconocete.php — test privado: solo miembros activos, terapeuta, o consultantes a quienes el terapeuta se lo asignó.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/tests-nucleo.php';
list($pdo, $usuario, $cat) = abrirTest('reconocete');
paginaTestInicio('reconocete', $cat, 'Guía de autoobservación, no un diagnóstico clínico ni una prueba psicométrica validada. Lee cada patrón con calma y reconócete en lo que resuene.');
?>
<div class="section-head left" style="max-width:760px;">
      <span class="small-caps eyebrow-gap">Cómo usar este test</span>
      <h2>Una pregunta a la vez, sin apuro</h2>
      <p>Primero el test del Eneatipo, luego el de las Heridas de la Infancia. Las preguntas salen en orden aleatorio y respondes Sí, No o No me identifico — con lo primero que sientas, sin pensarlo demasiado. Al final de cada uno verás qué patrón predomina en tus respuestas, con su descripción completa.</p>
    </div>

    <div class="section-head left" id="seccion-eneatipos" style="max-width:760px;margin-top:44px;">
      <span class="small-caps eyebrow-gap">Primera parte</span>
      <h2>Los 9 Eneatipos</h2>
    </div>
    <div id="quiz-eneatipos" style="margin-top:24px;max-width:640px;margin-left:auto;margin-right:auto;"></div>

    <div class="section-head left" id="seccion-heridas" style="max-width:760px;margin-top:56px;">
      <span class="small-caps eyebrow-gap">Segunda parte</span>
      <h2>Las 5 Heridas de la Infancia</h2>
    </div>
    <div id="quiz-heridas" style="margin-top:24px;max-width:640px;margin-left:auto;margin-right:auto;"></div>

    <div class="genea-descargar-wrap">
      <button type="button" id="btn-descargar-reconocete" class="btn btn-primary">Descargar mi resultado</button>
      <p>Se descarga un archivo .html con tu resultado, el desglose de tus respuestas y las descripciones completas. Se abre en cualquier navegador y lo puedes guardar como PDF. Puedes volver cuantas veces quieras y descargar de nuevo.</p>
    </div>
<?php paginaTestFin('reconocete', ['reconocete', 'reconocete-test', 'reco-quiz', 'reconocete-descargar']);
