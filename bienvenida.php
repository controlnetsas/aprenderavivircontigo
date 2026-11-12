<?php
// bienvenida.php — página de bienvenida para quien inicia un proceso terapéutico. Se envía por correo o WhatsApp.
// Uso: /bienvenida.php?t=TOKEN  (el botón final lleva a evaluacion.php con el mismo enlace).
require_once __DIR__ . '/camino-nucleo.php';

$token = $_GET['t'] ?? '';
$tokenOk = is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token);
$destino = $tokenOk ? '/evaluacion.php?t=' . $token : '';

paginaInicio('Bienvenida a tu proceso');
?>
<div class="hero" style="text-align:center;padding:38px 24px">
  <p style="font-size:13px;letter-spacing:.12em;text-transform:uppercase;color:#F5C343;margin:0">Reconoce tu esencia</p>
  <h1 style="font-size:2rem">Bienvenido/a a tu proceso</h1>
  <p style="font-size:17px;max-width:46ch;margin:.6em auto 0">Dar este paso ya es un acto de valentía. Gracias por confiarme tu historia: aquí te voy a acompañar con respeto, sin juicio y a tu propio ritmo.</p>
</div>

<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Qué es este espacio</h2>
  <p>Aprender a Vivir Contigo es un espacio de acompañamiento terapéutico y de autoconocimiento, inspirado en el enfoque sistémico de las constelaciones familiares y en la aceptología. No venimos a señalar culpables ni a "arreglarte": venimos a mirar con honestidad lo que vives, reconocer de dónde viene y encontrar, paso a paso, una forma más ligera de habitarlo.</p>
</div>

<div class="card" style="margin-bottom:16px">
  <h2 style="margin-top:0">Cómo será tu camino</h2>
  <p><span class="nbadge">1</span><strong>Tu evaluación inicial.</strong> Creas tu cuenta, llenas una ficha con tus datos básicos y haces tres tests de autoindagación: genealogía, eneatipos y heridas de la infancia. Toma entre 45 y 60 minutos, y puedes pausar y volver cuando quieras.</p>
  <p><span class="nbadge">2</span><strong>Revisión.</strong> Al terminar, recibo un informe con lo que compartiste. Lo leo con atención para llegar preparado a nuestra sesión.</p>
  <p><span class="nbadge">3</span><strong>Nuestra sesión.</strong> Conversamos sobre lo que apareció, definimos juntos qué quieres trabajar y qué pasos darás.</p>
  <p><span class="nbadge">4</span><strong>Seguimiento.</strong> Tu ficha y tus resultados quedan guardados de forma privada para que podamos ver tu evolución en las próximas consultas.</p>
</div>

<div class="card" style="margin-bottom:16px;border:2px solid #F5C343">
  <h2 style="margin-top:0">Un lugar seguro: nuestro acuerdo</h2>
  <h3>Lo que yo me comprometo a hacer</h3>
  <ul>
    <li>Tratar lo que me cuentes con <strong>confidencialidad</strong>: solo yo veo tu ficha, tus respuestas y tu informe.</li>
    <li>Escucharte con <strong>respeto y sin juicio</strong>, sea cual sea tu historia.</li>
    <li>Ir a tu ritmo: nunca te obligaré a hablar de lo que no estés listo/a para tocar.</li>
    <li>Ser claro sobre mis límites: lo que hago <strong>acompaña, no reemplaza</strong> la atención médica, psiquiátrica o psicológica.</li>
    <li>Borrar tus datos si me lo pides por WhatsApp.</li>
  </ul>
  <h3>Lo que te invito a cuidar tú</h3>
  <ul>
    <li><strong>Ser honesto/a contigo</strong> al responder. No hay respuestas buenas ni malas.</li>
    <li><strong>Parar</strong> si algo te remueve más de lo que puedes sostener, y avisarme. Respirar, tomar agua y continuar otro día es parte del proceso.</li>
    <li>Hacer los tests en un momento y lugar <strong>tranquilos</strong>, donde nadie te interrumpa.</li>
    <li>Mantener tu cuenta y tu contraseña <strong>solo para ti</strong>.</li>
    <li>Seguir tu tratamiento médico o psicológico si ya tienes uno, y contármelo.</li>
  </ul>
</div>

<div class="card" style="margin-bottom:16px;background:#fff8e6">
  <h2 style="margin-top:0">Si en algún momento te sientes mal</h2>
  <p>Remover la historia familiar y emocional puede traer tristeza, rabia o cansancio. Es normal y suele pasar. Pero si sientes que no puedes con lo que aparece, o tienes pensamientos de hacerte daño, <strong>no esperes a nuestra sesión</strong>: llama a la <strong>línea 123</strong> (emergencias en Colombia), ve a urgencias o busca a una persona de confianza. Después escríbeme: aquí estaré para acompañarte.</p>
</div>

<div class="card" style="text-align:center">
  <h2 style="margin-top:0">¿Listo/a para empezar?</h2>
  <p>Cuando quieras, da el primer paso. Si tienes dudas antes, escríbeme: con gusto las resuelvo.</p>
  <p class="fila" style="justify-content:center">
    <?php if ($destino): ?><a class="btn btn-primary" href="<?= h($destino) ?>">Comenzar mi evaluación</a><?php endif; ?>
    <a class="btn btn-outline-dark" target="_blank" rel="noopener" href="https://wa.me/<?= h(WHATSAPP_AVC) ?>?text=<?= rawurlencode('Hola Carlos, tengo una duda sobre mi proceso.') ?>">Escribirme por WhatsApp</a>
  </p>
  <?php if (!$destino): ?><p class="mut">Para comenzar, usa el enlace personal que te envió tu terapeuta.</p><?php endif; ?>
  <p class="mut" style="margin-top:14px">Con cariño, Carlos Andrés Echeverry<br>Aprender a Vivir Contigo</p>
</div>
<?php paginaFin();
