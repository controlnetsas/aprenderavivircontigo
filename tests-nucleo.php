<?php
// tests-nucleo.php — estructura común de las páginas de tests privados.
function paginaTestInicio($clave, $cat, $intro) {
  paginaInicio($cat['nombre']);
  echo '<style>' . <<<'CSS'
.wrap{max-width:1040px}
#quiz-eneatipos,#quiz-heridas,#quiz-vientre{max-width:900px!important}
.reco-quiz-progreso span{font-size:1.1rem}
.reco-quiz-progreso-nota{font-size:1.05rem}
.reco-quiz-barra-fondo{height:12px}
.reco-quiz-pregunta{padding:44px 40px}
.reco-quiz-pregunta p{font-size:1.7rem;line-height:1.45}
.reco-quiz-numero{width:46px;height:46px;font-size:1.1rem}
.reco-quiz-btn{font-size:1.2rem;padding:16px 32px}
.reco-quiz-nav .btn{font-size:1.05rem;padding:12px 26px}
.reco-resultado p,.reco-resultado li{font-size:1.15rem;line-height:1.65}
.avc-ico{display:inline-flex;align-items:center;justify-content:center;width:84px;height:84px;border-radius:50%;background:var(--crema-refugio);border:2px solid rgba(224,169,46,.55);color:var(--rosa-vinculo);margin-bottom:10px}
.avc-ejemplo{background:var(--crema-refugio);border-left:5px solid var(--dorado-esencia);border-radius:12px;padding:14px 20px;margin:0 auto 28px;text-align:left;max-width:740px}
.avc-ejemplo-etq{font-family:Poppins,Arial,sans-serif;font-weight:700;font-size:.82rem;letter-spacing:.08em;text-transform:uppercase;color:var(--dorado-tierra)}
.reco-quiz-pregunta .avc-ejemplo p{font-family:Lora,Georgia,serif;font-size:1.2rem;font-style:italic;line-height:1.55;margin:6px 0 0;color:var(--texto-cuerpo)}
.reco-texto{font-size:1.35rem;line-height:1.5}
.reco-opciones label,.reco-quiz-btn{font-size:1.1rem}
.reco-opciones label{padding:12px 24px}
.avc-fila{display:flex;gap:18px;align-items:flex-start}
.avc-fila .avc-ico{width:60px;height:60px;flex:none;margin:0}
.avc-fila-texto{flex:1;min-width:0}
.avc-fila .avc-ejemplo{margin:10px 0 14px;padding:12px 16px;max-width:none}
.avc-fila .avc-ejemplo p{font-family:Lora,Georgia,serif;font-size:1.1rem;font-style:italic;line-height:1.55;margin:5px 0 0}
.genea-pregunta label{font-size:1rem}
.genea-pregunta textarea{font-size:1.1rem}
.genea-contexto{font-size:1.15rem;line-height:1.65}
.perfil-nombre{font-size:1.25rem}
@media (max-width:600px){
  .reco-quiz-pregunta{padding:30px 18px}
  .reco-quiz-pregunta p{font-size:1.4rem}
  .reco-quiz-btn{width:100%;font-size:1.1rem}
  .reco-quiz-pregunta .reco-opciones{flex-direction:column}
  .avc-ico{width:70px;height:70px}
  .avc-fila{flex-direction:column;gap:8px}
  .reco-texto{font-size:1.2rem}
}
CSS
     . '</style>';
  echo '<p class="mut"><a href="/mis-tests.php">← Mis tests</a> · <a href="/mi-camino.php">Mi camino</a></p>';
  echo '<h1>' . h($cat['nombre']) . '</h1>';
  echo '<div class="disclaimer" style="max-width:100%;">' . h($intro)
     . ' Tus respuestas se escriben aquí. Solo se guardan en tu proceso si tú lo decides al final, con el botón "Guardar mi resultado".</div>';
}

function paginaTestFin($clave, $scripts) {
  $csrf = tokenCsrf();
  echo '<div id="guardar-panel" style="margin-top:36px"></div>';
  echo '<script src="/assets/js/test-ayudas.js"></script>';
  echo '<script>window.AVC_TEST_CLAVE=' . json_encode($clave) . ';window.AVC_CSRF=' . json_encode($csrf) . ';'
     . (!empty($_GET['eval']) ? 'window.AVC_VOLVER="/mi-evaluacion.php";' : '') . '</script>';
  foreach ($scripts as $s) echo '<script src="/assets/js/' . h($s) . '.js"></script>';
  echo '<script src="/assets/js/guardar-test.js"></script>';
  paginaFin();
}
