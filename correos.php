<?php
// correos.php — envío de correos transaccionales con Brevo y enlaces de WhatsApp.
// Usa BREVO_API_KEY de config.php (la misma clave que ya usa sincronizacion.php).
require_once file_exists(__DIR__ . '/../config.php') ? __DIR__ . '/../config.php' : __DIR__ . '/config.php';

if (!defined('SITIO_URL')) define('SITIO_URL', 'https://aprenderavivircontigo.com');

function textoSeguro($s) {
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Envía un correo con la identidad visual de la marca. Devuelve true si Brevo lo aceptó.
function enviarCorreo($email, $nombre, $asunto, $contenidoHtml) {
  if (!defined('BREVO_API_KEY') || !BREVO_API_KEY) return false;

  $html = '<div style="font-family:Poppins,Arial,sans-serif;max-width:520px;margin:0 auto;padding:32px;background:#FDF9F2;border-radius:16px;color:#211A33;line-height:1.55;">'
        . $contenidoHtml
        . '<hr style="border:none;border-top:1px solid #e8dfd0;margin:28px 0 14px;">'
        . '<p style="font-size:12px;color:#6b6478;margin:0;">Aprender a Vivir Contigo · Reconoce tu esencia<br>'
        . 'Si no quieres seguir recibiendo estos mensajes, escríbenos por WhatsApp al +57 317 643 5131 y los detenemos.</p>'
        . '</div>';

  $body = [
    'sender' => ['name' => 'Aprender a Vivir Contigo', 'email' => 'talleres@aprenderavivircontigo.com'],
    'to' => [['email' => $email, 'name' => $nombre]],
    'subject' => $asunto,
    'htmlContent' => $html,
  ];

  $ch = curl_init('https://api.brevo.com/v3/smtp/email');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($body),
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => [
      'api-key: ' . BREVO_API_KEY,
      'Content-Type: application/json',
    ],
  ]);
  curl_exec($ch);
  $codigo = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return $codigo >= 200 && $codigo < 300;
}

function botonCorreo($url, $texto) {
  return '<p style="text-align:center;margin:26px 0;"><a href="' . textoSeguro($url)
    . '" style="background:#F5C343;color:#211A33;padding:14px 28px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;">'
    . textoSeguro($texto) . '</a></p>';
}

// Correo de invitación: solo contiene el enlace, nunca contenido clínico de la consulta.
function correoInvitacion($nombre, $link) {
  $primer = explode(' ', trim($nombre))[0];
  return '<h2 style="margin-top:0;">Hola, ' . textoSeguro($primer) . '</h2>'
    . '<p>Gracias por tu consulta. Preparé un camino de acompañamiento para ti, con ejercicios diarios y los talleres que pueden servirte como siguiente paso.</p>'
    . '<p>Para activarlo, entra con el botón y crea tu cuenta con tu nombre y una contraseña:</p>'
    . botonCorreo($link, 'Activar mi camino')
    . '<p style="font-size:13px;">Si el botón no funciona, copia este enlace en tu navegador:<br>' . textoSeguro($link) . '</p>'
    . '<p style="font-size:13px;">El enlace es personal y vence en 14 días.</p>';
}

function correoEjercicio($nombre, $dia, $total, $titulo, $baseNombre, $duracion, $instrucciones, $esButeyko) {
  $primer = explode(' ', trim($nombre))[0];
  $meta = textoSeguro($baseNombre) . ($duracion ? ' · ' . (int)$duracion . ' min' : '');
  $aviso = $esButeyko
    ? '<p style="font-size:12px;color:#6b6478;">Si tienes una condición cardíaca o respiratoria, epilepsia o estás embarazada, consulta con tu médico antes de practicar ejercicios de respiración. Suspende si sientes mareo.</p>'
    : '';
  return '<h2 style="margin-top:0;">Hola, ' . textoSeguro($primer) . '</h2>'
    . '<p style="margin:0 0 4px;font-size:13px;color:#C2607D;font-weight:600;">Día ' . (int)$dia . ' de ' . (int)$total . ' · ' . $meta . '</p>'
    . '<h3 style="margin:0 0 12px;">' . textoSeguro($titulo) . '</h3>'
    . '<p>' . nl2br(textoSeguro($instrucciones)) . '</p>'
    . $aviso
    . botonCorreo(SITIO_URL . '/mi-camino.php', 'Abrir mi camino y marcar como hecho');
}

// ---- WhatsApp (enlaces wa.me: la persona toca "enviar"; no es envío automático) ----
function telefonoWhatsApp($tel) {
  $d = preg_replace('/\D+/', '', (string)$tel);
  if ($d === '') return '';
  if (strlen($d) === 10 && $d[0] === '3') return '57' . $d; // celular colombiano sin indicativo
  return $d;
}

function linkWhatsApp($tel, $mensaje) {
  return 'https://wa.me/' . telefonoWhatsApp($tel) . '?text=' . rawurlencode($mensaje);
}
