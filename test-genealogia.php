<?php
// test-genealogia.php — test privado: solo miembros activos, terapeuta, o consultantes a quienes el terapeuta se lo asignó.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
require_once __DIR__ . '/tests-nucleo.php';
list($pdo, $usuario, $cat) = abrirTest('genealogia');
paginaTestInicio('genealogia', $cat, 'Un espacio guiado de autoindagación sobre tu historia familiar. No se puntúa: es para observarte con calma, tema por tema. Si algo remueve una emoción intensa, está bien detenerte; para eso está el acompañamiento.');
?>
<div class="section-head left" style="max-width:760px;">
      <span class="small-caps eyebrow-gap">Antes de los seis temas</span>
      <h2>Construye tu árbol</h2>
      <p>Agrega los ancestros que quieras recordar — padres, abuelos, hermanos u otros. Con cada uno, indica el vínculo familiar y por qué lo recuerdas. Con esa información arma tu árbol y detectamos posibles lealtades familiares y lenguaje nuclear heredado.</p>
    </div>
    <div id="arbol-genealogico-wizard" style="margin-top:24px;"></div>

    <div class="section-head left" style="max-width:760px;margin-top:56px;">
      <span class="small-caps eyebrow-gap">Los seis temas</span>
      <h2>Responde con lo primero que aparezca</h2>
      <p>Recorre los seis temas del taller en el orden que quieras. No busques la respuesta "correcta" — responde con lo primero que sientas. Puedes dejar preguntas sin responder y volver a ellas después.</p>
    </div>
    <div id="lista-genealogia-test" style="margin-top:28px;"></div>
    <div id="genea-resumen-temas" style="margin-top:40px;"></div>

    <div class="genea-descargar-wrap">
      <button type="button" id="btn-descargar-genealogia" class="btn btn-primary">Descargar mi árbol y respuestas</button>
      <p>Se descarga un archivo .html con tu árbol genealógico, el contexto de trauma heredado y todas tus respuestas. Se abre en cualquier navegador y lo puedes guardar como PDF. Puedes volver cuantas veces quieras y descargar de nuevo.</p>
    </div>
<?php paginaTestFin('genealogia', ['genealogia-arbol', 'reco-quiz', 'genealogia-test', 'genealogia-test-render']);
