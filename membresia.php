<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sesion.php';

$usuario = usuarioActual(); // null si no hay sesión iniciada
$esMiembro = esMiembroActivo($usuario);

// Evaluación inicial del terapeuta (si la persona se registró con su enlace)
$evaluacion = null;
if ($usuario) {
  try {
    require_once __DIR__ . '/camino-nucleo.php';
    $evaluacion = evaluacionDeUsuario(conexionDB(), $usuario['id']);
  } catch (Throwable $e) { $evaluacion = null; }
}

$contenido = [];
if ($esMiembro) {
  $pdo = conexionDB();
  $hoy = date('Y-m-d');
  $stmt = $pdo->prepare(
    "SELECT * FROM contenido_valor
     WHERE (vigente_desde IS NULL OR vigente_desde <= ?)
       AND (vigente_hasta IS NULL OR vigente_hasta >= ?)
     ORDER BY tipo, titulo"
  );
  $stmt->execute([$hoy, $hoy]);
  $contenido = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$ETIQUETAS_TIPO = [
  'meditacion'  => 'Meditaciones guiadas',
  'respiracion' => 'Métodos de respiración',
  'receta'      => 'Recetas',
  'rutina'      => 'Rutinas de alimentación',
  'libro'       => 'Libros y guías descargables',
  'blog'        => 'Artículos del blog',
  'taller'      => 'Grabaciones de talleres',
  'descuento'   => 'Descuentos activos',
];
$agrupado = [];
foreach ($contenido as $item) { $agrupado[$item['tipo']][] = $item; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Club de Membresía | Aprender a Vivir Contigo</title>
<meta name="description" content="Accede al Club de Membresía de Aprender a Vivir Contigo: libros descargables, artículos del blog, descuentos y grabaciones de talleres por tiempo limitado.">
<link rel="icon" type="image/png" href="assets/img/favicon-32.png">
<link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#211A33">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="container">
    <a href="index.html" class="brand">
      <img src="assets/img/logo.png" alt="Aprender a Vivir Contigo">
      <span>Aprender a Vivir Contigo<small>Reconoce tu esencia</small></span>
    </a>
    <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false"><span></span><span></span><span></span></button>
    <nav class="main-nav">
      <a href="index.html">Inicio</a>
      <a href="sobre-la-marca.html">Nuestro Propósito</a>
      <a href="talleres.html">Talleres</a>
      <a href="descubrete.html">Reconóce TE</a>
      <a href="mi-genealogia.html">Mi Genealogía</a>
      <a href="terapias-grupales.html">Terapias Grupales</a>
      <a href="agenda-te.html">Agenda TE</a>
      <a href="contacto.html">Contacto</a>
      <a href="https://wa.me/573176435131" class="nav-cta" target="_blank" rel="noopener">Escríbenos</a>
    </nav>
  </div>
</header>

<section class="page-hero">
  <div class="container">
    <span class="small-caps on-dark eyebrow-gap">Club de Membresía</span>
    <h1>Contenido de valor para quienes ya viven el proceso</h1>
    <p>Talleres, libros y descuentos disponibles por tiempo limitado mientras tu membresía esté activa.</p>
  </div>
</section>
<div class="container" style="padding:18px 0;text-align:center;">
  <a class="btn btn-outline-dark" href="/mi-camino.php">Mi camino · ejercicios y talleres</a>
  <a class="btn btn-outline-dark" href="/mis-tests.php">Mis tests</a>
  <a class="btn btn-outline-dark" href="/cambiar-clave.php">Cambiar contraseña</a>
  <?php if (!empty($usuario) && ($usuario['rol'] ?? '') === 'terapeuta'): ?>
    <a class="btn btn-primary" href="/portal-terapeuta.php">Portal del terapeuta</a>
  <?php endif; ?>
</div>

<?php if ($usuario && $evaluacion): $evPend = ($evaluacion['estado'] ?? '') !== 'completada'; ?>
<section style="padding-bottom:0">
  <div class="container">
    <div class="card" style="max-width:720px;margin:0 auto;border:2px solid #F5C343;background:linear-gradient(135deg,#FFF6D9,#FFF3F6);text-align:center;">
      <span class="small-caps eyebrow-gap"><?= $evPend ? 'Tu primer paso' : 'Evaluación enviada' ?></span>
      <?php if ($evPend): ?>
        <h3 class="mt-0">Tu evaluación inicial te espera</h3>
        <p>Llena tu ficha y haz tus tests. Puedes pausar y continuar cuando quieras. Al terminar, tu terapeuta recibe tu informe.</p>
        <a href="/mi-evaluacion.php" class="btn btn-primary" style="font-size:1.1rem;padding:14px 30px;">Continuar mi evaluación</a>
      <?php else: ?>
        <h3 class="mt-0">Tu evaluación ya fue enviada</h3>
        <p>Tu terapeuta ya tiene tu informe. Puedes revisar tus resultados cuando quieras.</p>
        <a href="/mi-evaluacion.php" class="btn btn-outline-dark">Ver mi evaluación</a>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="container">

    <?php if (!$usuario): ?>
      <!-- No hay sesión iniciada -->
      <div class="card" style="max-width:480px;margin:0 auto;">
        <span class="small-caps eyebrow-gap">Necesitas iniciar sesión</span>
        <h3 class="mt-0">Ingresa a tu cuenta</h3>
        <p>Para ver el contenido del club primero debes iniciar sesión (o crear tu cuenta si es tu primera vez).</p>
        <a href="login.html" class="btn btn-primary btn-block">Ir a Ingresar</a>
      </div>

    <?php elseif (!$esMiembro): ?>
      <?php $acabo = ($usuario['estado_membresia'] === 'vencida') || (!empty($usuario['fecha_vencimiento']) && strtotime($usuario['fecha_vencimiento']) < time()); ?>
      <!-- Hay sesión, pero la membresía no está activa (pendiente, vencida o cancelada) -->
      <div class="card" style="max-width:560px;margin:0 auto;">
        <span class="small-caps eyebrow-gap">Hola, <?= htmlspecialchars(explode(' ', $usuario['nombre'])[0]) ?></span>
        <?php if ($acabo): ?>
          <h3 class="mt-0">Tu acceso terminó<?= !empty($usuario['fecha_vencimiento']) ? ' el ' . date('d/m/Y', strtotime($usuario['fecha_vencimiento'])) : '' ?></h3>
          <p>Gracias por haber hecho parte de este proceso. Para seguir con el contenido del club, puedes comprar tu membresía; y si quieres continuar tu acompañamiento personal, pide una nueva consulta.</p>
          <a href="agenda-te.html#membresia" class="btn btn-primary btn-block">Comprar mi membresía</a>
          <a href="https://wa.me/573176435131?text=<?= rawurlencode('Hola Carlos, mi acceso terminó y quiero pedir una nueva consulta.') ?>" target="_blank" rel="noopener" class="btn btn-outline-dark btn-block" style="margin-top:10px;">Pedir una nueva consulta</a>
        <?php else: ?>
          <h3 class="mt-0">Tu membresía no está activa</h3>
          <p>Aún no tienes una membresía activa. Actívala para desbloquear talleres, libros y descuentos por tiempo limitado.</p>
          <a href="agenda-te.html#membresia" class="btn btn-primary btn-block">Activar membresía</a>
        <?php endif; ?>
      </div>

    <?php else: ?>
      <!-- Miembro activo: se muestra el contenido real, servido desde la base de datos -->
      <div class="disclaimer" style="max-width:100%;">
        Tu membresía está activa<?= $usuario['fecha_vencimiento'] ? ' hasta el ' . date('d/m/Y', strtotime($usuario['fecha_vencimiento'])) : '' ?>. Este contenido puede cambiar por tiempo limitado — vuelve seguido.
      </div>

      <div class="text-center" style="margin-top:20px;">
        <button id="btn-instalar-app" class="btn btn-outline-dark" type="button" style="display:none;">📲 Instalar como app en tu celular</button>
      </div>

      <div style="margin-top:32px;">
        <?php if (empty($contenido)): ?>
          <p>Muy pronto agregaremos aquí el contenido del club.</p>
        <?php else: ?>
          <?php foreach ($ETIQUETAS_TIPO as $tipo => $titulo): if (empty($agrupado[$tipo])) continue; ?>
            <div style="margin-bottom:32px;">
              <span class="small-caps eyebrow-gap"><?= htmlspecialchars($titulo) ?></span>
              <div class="grid-2" style="margin-top:14px;">
                <?php
                  $ETIQUETAS_BOTON = ['meditacion' => 'Reproducir', 'respiracion' => 'Reproducir', 'receta' => 'Ver receta', 'rutina' => 'Ver rutina', 'libro' => 'Descargar / Ver'];
                ?>
                <?php foreach ($agrupado[$tipo] as $item): ?>
                  <div class="card" style="margin-bottom:18px;">
                    <h4 class="mt-0"><?= htmlspecialchars($item['titulo']) ?></h4>
                    <?php if (!empty($item['duracion_min'])): ?>
                      <p style="font-size:0.8rem;color:var(--rosa-vinculo);margin:-6px 0 8px;">⏱ <?= (int)$item['duracion_min'] ?> min</p>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($item['descripcion']) ?></p>
                    <?php if ($item['url_archivo']): ?>
                      <a class="btn btn-primary btn-block" href="<?= htmlspecialchars($item['url_archivo']) ?>" target="_blank" rel="noopener"><?= $ETIQUETAS_BOTON[$tipo] ?? 'Abrir' ?></a>
                    <?php else: ?>
                      <span class="btn btn-outline-dark btn-block" style="opacity:0.6;pointer-events:none;">Próximamente</span>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<section class="section-dark">
  <div class="container text-center">
    <span class="small-caps on-dark eyebrow-gap">¿Tienes dudas?</span>
    <h2>Escríbenos si algo no te carga</h2>
    <p style="max-width:60ch;margin:0 auto 24px;color:rgba(255,255,255,0.8);">Este club está en crecimiento — cada mes sumamos más contenido. Si tu compra no se refleja aquí, escríbenos y lo revisamos contigo.</p>
    <a href="https://wa.me/573176435131?text=Hola%2C%20tengo%20una%20duda%20sobre%20el%20Club%20de%20Membres%C3%ADa" target="_blank" rel="noopener" class="btn btn-primary">Escríbenos por WhatsApp</a>
  </div>
</section>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand">
          <img src="assets/img/logo.png" alt="">
          <strong>Aprender a Vivir Contigo</strong>
        </div>
        <p class="footer-tagline">Reconocer para reconciliar.</p>
        <p>Talleres de constelaciones familiares y aceptología en Colombia, inspirados en el enfoque sistémico de Bert Hellinger.</p>
      </div>
      <div>
        <h4>Explora</h4>
        <ul class="footer-links">
          <li><a href="index.html">Inicio</a></li>
          <li><a href="talleres.html">Talleres</a></li>
          <li><a href="agenda-te.html">Agenda TE</a></li>
          <li><a href="membresia.php">Club de Membresía</a></li>
          <li><a href="contacto.html">Contacto</a></li>
        </ul>
      </div>
      <div>
        <h4>Contacto</h4>
        <ul class="footer-contact">
          <li><img src="assets/img/icon-whatsapp.png" alt="">317 643 5131</li>
          <li>talleres@aprenderavivircontigo.com</li>
          <li>www.aprenderavivircontigo.com</li>
        </ul>
      </div>
    </div>
    <div class="footer-bar">
      <span>© 2026 Aprender a Vivir Contigo. Todos los derechos reservados.</span>
      <div class="social">
        <a href="https://wa.me/573176435131" target="_blank" rel="noopener"><img src="assets/img/icon-whatsapp.png" alt="WhatsApp"></a>
        <a href="https://www.facebook.com/aprenderavivircontigo" target="_blank" rel="noopener"><img src="assets/img/icon-facebook.png" alt="Facebook"></a>
        <a href="https://instagram.com/aprenderavivircontigo" target="_blank" rel="noopener"><img src="assets/img/icon-instagram.png" alt="Instagram"></a>
      </div>
    </div>
  </div>
</footer>
<a class="wa-float" href="https://wa.me/573176435131?text=Hola%2C%20tengo%20una%20duda%20sobre%20el%20Club%20de%20Membres%C3%ADa" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp">
  <img src="assets/img/icon-whatsapp.png" alt="">
</a>
<script src="assets/js/main.js"></script>
</body>
</html>
