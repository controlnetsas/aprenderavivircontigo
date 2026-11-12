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
.bv-hero-cta{margin:18px auto 12px!important;font-family:Poppins,Arial,sans-serif;font-size:19px!important;color:#F5C343!important}.bv-hero-btn{display:inline-block;background:#F5C343;color:#211A33;font-family:Poppins,Arial,sans-serif;font-weight:600;border-radius:30px;padding:12px 30px;text-decoration:none}
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
.bv-reg h2{font-size:1.8rem}.bv-reg{padding:38px 22px;box-shadow:0 10px 30px rgba(245,195,67,.45)}
.bv-reg .btn-primary{background:#211A33;color:#fff;font-size:19px;padding:17px 34px}.bv-form .btn-primary{font-size:18px;padding:16px;justify-content:center;text-align:center}
.bv-reg .btn-outline-dark{background:rgba(255,255,255,.6)}
.bv-reg .mut{color:#211A33;opacity:.8;font-size:14px}
.bv-login{background:#fff;max-width:560px;width:100%;box-sizing:border-box;padding:20px 22px;margin:0 auto 16px;box-shadow:0 6px 24px rgba(33,26,51,.08)}.bv-login .etq{display:block;font-size:13px;font-weight:600;margin:12px 0 3px}.bv-login .btn-primary{background:#F5C343;color:#211A33;font-size:17px;padding:14px}
.bv-wa{background:#e9f8ef;text-align:center;max-width:560px;margin:0 auto 16px;padding:18px 22px}.bv-wa .btn{background:#fff;border:2px solid #25D366;color:#211A33}
.bv-firma{text-align:center;color:#6b6480;font-size:14px;margin-top:6px}
.bv-barra{position:fixed;left:0;right:0;bottom:0;background:#211A33;padding:10px 16px;text-align:center;z-index:50;box-shadow:0 -4px 16px rgba(0,0,0,.2)}
.bv-barra a{display:inline-block;background:#F5C343;color:#211A33;font-family:Poppins,Arial,sans-serif;font-weight:600;border-radius:30px;padding:11px 26px;text-decoration:none}
.bv-form{display:block;background:#fff;border-radius:16px;padding:20px 22px;text-align:left;margin:16px auto 0;max-width:560px;width:100%;box-sizing:border-box;box-shadow:0 6px 20px rgba(33,26,51,.15)}
.bv-form .etq{display:block;font-size:13px;font-weight:600;margin:10px 0 3px;color:#211A33}
.bv-form .g2{display:grid;grid-template-columns:1fr 1fr;gap:0 14px}
.bv-form .mut{color:#6b6480;opacity:1;font-size:13px}
@media(min-width:900px){.bv-barra{display:none}}
@media(max-width:600px){.bv-login,.bv-wa{margin-left:22px;margin-right:22px;width:auto}.bv-form .g2{grid-template-columns:1fr}.bv-hero h1{font-size:1.6rem}.pasos4{grid-template-columns:1fr}}
</style>

<div class="bv bv-hero">
  <p class="bv-eti">Reconoce tu esencia</p>
  <h1>Bienvenido/a a tu proceso</h1>
  <p>Dar este paso es un acto de valentía. Aquí te acompaño con respeto, sin juicio y a tu ritmo.</p>
  <p class="bv-hero-cta"><strong>Regístrate para iniciar el camino de Aprender a Vivir Contigo</strong></p>
  <a class="bv-hero-btn" href="#registro">Registrarme ahora</a>
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

<div class="bv bv-reg" id="registro">
  <h2>Regístrate para iniciar el camino de Aprender a Vivir Contigo</h2>
  <p style="font-size:18px">Crea tu cuenta. Te toma un par de minutos.</p>
  <form class="bv-form" method="post" action="<?= $destino ? '/evaluacion.php' : '/registro-cuenta.php' ?>" autocomplete="off">
    <?php if ($destino): ?><input type="hidden" name="t" value="<?= h($token) ?>"><?php endif; ?>
    <div style="position:absolute;left:-9999px" aria-hidden="true"><input name="sitio_web" tabindex="-1" autocomplete="off"></div>
    <label class="etq" for="b-nombre">Nombre completo*</label>
    <input class="campo" id="b-nombre" name="nombre" required maxlength="150">
    <div class="g2">
      <div><label class="etq" for="b-correo">Correo electrónico*</label>
      <input class="campo" id="b-correo" name="correo" type="email" required maxlength="190"></div>
      <div><label class="etq" for="b-cel">Número de celular*</label>
      <input class="campo" id="b-cel" name="<?= $destino ? 'celular' : 'whatsapp' ?>" type="tel" required maxlength="30" placeholder="3001234567"></div>
      <div><label class="etq" for="b-p1">Contraseña* (mín. 8)</label>
      <input class="campo" id="b-p1" name="password" type="password" minlength="8" required autocomplete="new-password"></div>
      <div><label class="etq" for="b-p2">Confirma tu contraseña*</label>
      <input class="campo" id="b-p2" name="password2" type="password" minlength="8" required autocomplete="new-password"></div>
    </div>
    <?php if ($destino): ?>
    <label style="display:flex;gap:10px;margin:14px 0;align-items:flex-start">
      <input type="checkbox" name="consentimiento" value="1" required style="margin-top:4px">
      <span class="mut"><?= h(TEXTO_CONSENTIMIENTO_EVAL) ?></span>
    </label>
    <?php else: ?><div style="height:14px"></div><?php endif; ?>
    <button class="btn btn-primary btn-block" type="submit" style="width:100%;flex-direction:column;line-height:1.3;white-space:normal;border-radius:28px;padding:14px 16px">Crear mi cuenta<small style="display:block;font-weight:600;font-size:17px;margin-top:6px;text-align:center">¡Después de este click vendrán muchos cambios!</small></button>
    <p class="mut" style="margin:10px 0 0;text-align:center">Te enviaremos un correo para confirmar que es tuyo.</p>
  </form>
</div>
<div class="bv bv-wa">
  <p style="margin:0 0 10px">¿Tienes dudas antes de empezar?</p>
  <a class="btn btn-outline-dark" target="_blank" rel="noopener" href="<?= h($wa) ?>">Escríbeme por WhatsApp</a>
</div>
<p class="bv-firma">Con cariño, Aprender a Vivir Contigo</p>

<div class="bv-barra"><a href="#registro">Registrarme y comenzar</a></div><div style="height:50px"></div>
<script src="/assets/js/password.js"></script>
<?php paginaFin();
