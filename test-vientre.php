<?php
// test-vientre.php — test privado: solo miembros activos, terapeuta, o consultantes a quienes el terapeuta se lo asignó.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/tests-nucleo.php';
list($pdo, $usuario, $cat) = abrirTest('vientre');
paginaTestInicio('vientre', $cat, 'Guía de autoobservación, no un diagnóstico clínico ni una prueba psicométrica validada. Si aparece algo que te remueve fuerte, acompáñate de un profesional para procesarlo con cuidado.');
?>
<div class="section-head left" style="max-width:760px;">
      <span class="small-caps eyebrow-gap">Cómo usar este test</span>
      <h2>30 preguntas, una a la vez</h2>
      <p>15 preguntas exploran tu vínculo con la madre y 15 tu vínculo con el padre — mezcladas y presentadas en orden aleatorio, así que no hay un orden que puedas anticipar. Respondes Sí, No o No me identifico, con lo primero que sientas, sin pensarlo demasiado. Al final, un algoritmo simple pondera cuántos "Sí" marcaste en cada vínculo y te muestra cuál concentra más carga hoy.</p>
    </div>

    <div id="quiz-vientre" style="margin-top:24px;max-width:640px;margin-left:auto;margin-right:auto;"></div>

    <div class="genea-descargar-wrap">
      <button type="button" id="btn-descargar-vientre" class="btn btn-primary">Descargar mi resultado</button>
      <p>Se descarga un archivo .html con tu resultado, el desglose de tus respuestas y las descripciones completas. Se abre en cualquier navegador y lo puedes guardar como PDF. Puedes volver cuantas veces quieras y descargar de nuevo.</p>
    </div>
<?php paginaTestFin('vientre', ['vientre-test', 'reco-quiz', 'vientre-descargar']);
