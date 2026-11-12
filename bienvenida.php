<?php
// bienvenida.php — bienvenida para quien inicia un proceso terapéutico. Se envía por correo o WhatsApp.
// Uso: /bienvenida.php?t=TOKEN  (los botones de registro llevan a evaluacion.php con el mismo token).
require_once __DIR__ . '/camino-nucleo.php';

$token = $_GET['t'] ?? '';
$tokenOk = is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token);
$destino = $tokenOk ? '/evaluacion.php?t=' . $token : '';
$wa = 'https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, tengo una duda sobre mi proceso.');

paginaInicio('Bienvenida a tu proceso');
?>
<style>
.bv{border-radius:20px;padding:24px 22px;margin:0 0 16px}
.bv h2{margin:0 0 10px;font-size:1.25rem}
.bv p,.bv li{font-size:16px;margin:.35em 0}
.bv-eti{font-family:Poppins,Arial,sans-serif;font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:600;margin:0 0 4px}
.bv-hero{background:linear-gradient(135deg,#211A33,#4a2c5a 60%,#C2607D);color:#fff;text-align:center;padding:40px 22px}
.bv-hero h1{color:#fff;font-size:2rem;margin:.1em 0 .3em}
.bv-hero p{color:rgba(255,255,255,.92);max-width:44ch;margin:0 auto;font-size:17px}
.bv-hero .bv-eti{color:#F5C343}
.bv-que{background:#fff3f6;border-left:6px solid #C2607D}
.bv-camino{background:#fff}
.pasos4{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:10px}
.pasos4 div{background:#FDF9F2;border-radius:14px;padding:12px 14px;font-size:15px;line-height:1.45}
.pasos4 b{display:block;font-family:Poppins,Arial,sans-serif;margin-bottom:2px}
.bv-seguro{background:#211A33;color:#fff;border:2px solid #F5C343}
.bv-seguro h2,.bv-seguro h3{color:#F5C343}
.bv-seguro h3{font-size:1rem;margin:14px 0 4px}
.bv-seguro ul{margin:0;padding-left:20px}
.bv-seguro strong{color:#fff}
.bv-ayuda{background:#fff6d9;border-left:6px solid #F5C343}
.bv-reg{background:linear-gradient(135deg,#F5C343,#f3a65c);color:#211A33;text-align:center;padding:30px 22px}
.bv-reg h2{font-size:1.5rem}
.bv-reg .btn-primary{background:#211A33;color:#fff;font-size:17px;padding:14px 28px}
.bv-reg .btn-outline-dark{background:rgba(255,255,255,.6)}
.bv-reg .mut{color:#211A33;opacity:.8;font-size:14px}
.bv-firma{text-align:center;color:#6b6480;font-size:14px;margin-top:6px}
.bv-barra{position:fixed;left:0;right:0;bottom:0;background:#211A33;padding:10px 16px;text-align:center;z-index:50;box-shadow:0 -4px 16px rgba(0,0,0,.2)}
.bv-barra a{display:inline-block;background:#F5C343;color:#211A33;font-family:Poppins,Arial,sans-serif;font-weight:600;border-radius:30px;padding:11px 26px;text-decoration:none}
@media(max-width:600px){.bv-hero h1{font-size:1.6rem}.pasos4{grid-template-columns:1fr}}
</style>

<div class="bv bv-hero">
  <p class="bv-eti">Reconoce tu esencia</p>
  <h1>Bienvenido/a a tu proceso</h1>
  <p>Dar este paso es un acto de valentía. Aquí te acompaño con respeto, sin juicio y a tu ritmo.</p>
</div>

<div class="bv bv-que">
  <p class="bv-eti" style="color:#C2607D">El espacio</p>
  <h2>Qué es Aprender a Vivir Contigo</h2>
  <p>Un acompañamiento terapéutico y de autoconocimiento, desde las constelaciones familiares y la aceptología. No buscamos culpables ni "arreglarte": miramos con honestidad lo que vives, de dónde viene, y encontramos una forma más ligera de habitarlo.</p>
</div>

<div class="bv bv-camino">
  <p class="bv-eti" style="color:#C2607D">Tu camino</p>
  <h2>Cuatro pasos</h2>
  <div class="pasos4">
    <div><b>1 · Evaluación</b>Creas tu cuenta, llenas tu ficha y haces 3 tests (45–60 min, puedes pausar).</div>
    <div><b>2 · Revisión</b>Recibo tu informe y lo leo con atención.</div>
    <div><b>3 · Sesión</b>Conversamos y definimos qué quieres trabajar.</div>
    <div><b>4 · Seguimiento</b>Tu ficha queda guardada, privada, para ver tu evolución.</div>
  </div>
</div>

<div class="bv bv-seguro">
  <p class="bv-eti" style="color:#F5C343">Nuestro acuerdo</p>
  <h2>Un lugar seguro</h2>
  <h3>Yo me comprometo a</h3>
  <ul>
    <li><strong>Confidencialidad:</strong> solo yo veo tu información.</li>
    <li><strong>Respeto y sin juicio</strong>, sea cual sea tu historia.</li>
    <li><strong>Tu ritmo:</strong> nunca te obligo a tocar lo que no estés listo/a.</li>
    <li><strong>Acompañar, no reemplazar</strong> la atención médica o psicológica.</li>
    <li>Borrar tus datos si me lo pides.</li>
  </ul>
  <h3>Te invito a cuidar</h3>
  <ul>
    <li><strong>Honestidad contigo:</strong> no hay respuestas buenas ni malas.</li>
    <li><strong>Pausar</strong> si algo te remueve demasiado, y avisarme.</li>
    <li>Un lugar <strong>tranquilo</strong> y tu cuenta <strong>solo para ti</strong>.</li>
    <li>Seguir tu tratamiento si ya tienes uno.</li>
  </ul>
</div>

<div class="bv bv-ayuda">
  <h2>Si te sientes mal</h2>
  <p>Remover la historia puede traer tristeza, rabia o cansancio; es normal. Si sientes que no puedes con lo que aparece o piensas en hacerte daño, <strong>no esperes a la sesión</strong>: llama a la <strong>línea 123</strong>, ve a urgencias o busca a alguien de confianza. Luego escríbeme.</p>
</div>

<div class="bv bv-reg" id="registro">
  <h2>¿Listo/a para empezar?</h2>
  <?php if ($destino): ?>
    <p>Crea tu cuenta con tu nombre, correo y celular. Te toma un par de minutos.</p>
    <p class="fila" style="justify-content:center;margin-top:14px">
      <a class="btn btn-primary" href="<?= h($destino) ?>">Registrarme y comenzar</a>
    </p>
    <p class="mut" style="margin-top:12px">¿Ya tienes cuenta? <a href="/login.html" style="color:#211A33;font-weight:600">Inicia sesión</a></p>
  <?php else: ?>
    <p>Para registrarte, usa el enlace personal que te envió tu terapeuta.</p>
    <p class="mut">¿Ya tienes cuenta? <a href="/login.html" style="color:#211A33;font-weight:600">Inicia sesión</a></p>
  <?php endif; ?>
  <p class="fila" style="justify-content:center;margin-top:10px">
    <a class="btn btn-outline-dark" target="_blank" rel="noopener" href="<?= h($wa) ?>">¿Dudas? Escríbeme por WhatsApp</a>
  </p>
</div>
<p class="bv-firma">Con cariño, Carlos Andrés Echeverry · Aprender a Vivir Contigo</p>

<?php if ($destino): ?><div class="bv-barra"><a href="<?= h($destino) ?>">Registrarme y comenzar</a></div><div style="height:50px"></div><?php endif; ?>
<?php paginaFin();
