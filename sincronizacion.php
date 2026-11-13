<?php
require_once file_exists(__DIR__ . '/../config.php') ? __DIR__ . '/../config.php' : __DIR__ . '/config.php';
require_once __DIR__ . '/correos.php';

// IDs de las listas de Brevo, una por taller — reemplaza con los tuyos (Brevo → Contactos → Listas).
$MAPA_LISTAS_BREVO = [
  'Sanando con mi Linaje' => 2,
  'Regresando al Vientre' => 3,
  'Reconoce TE'           => 4,
  'Boletín general'       => null, // suscriptores generales del login, sin lista de taller específica
];

function sincronizarContactoHubSpot($nombre, $email) {
  if (!defined('HUBSPOT_PRIVATE_APP_TOKEN') || !HUBSPOT_PRIVATE_APP_TOKEN) { error_log('HubSpot: falta HUBSPOT_PRIVATE_APP_TOKEN en config.php'); return; }
  $partes = explode(' ', $nombre, 2);
  $data = json_encode([
    'properties' => [
      'email' => $email,
      'firstname' => $partes[0] ?? '',
      'lastname' => $partes[1] ?? '',
      'lifecyclestage' => 'lead',
    ]
  ]);

  $ch = curl_init('https://api.hubapi.com/crm/v3/objects/contacts');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => [
      'Authorization: Bearer ' . HUBSPOT_PRIVATE_APP_TOKEN,
      'Content-Type: application/json',
    ],
  ]);
  curl_exec($ch);
  curl_close($ch);
}

function sincronizarContactoBrevo($nombre, $email, $telefono, $taller) {
  if (!defined('BREVO_API_KEY') || !BREVO_API_KEY) { error_log('Brevo: falta BREVO_API_KEY en config.php'); return; }
  global $MAPA_LISTAS_BREVO;
  $partes = explode(' ', $nombre, 2);
  $listaId = $MAPA_LISTAS_BREVO[$taller] ?? null;

  $atributos = [
    'NOMBRE' => $partes[0] ?? '',
    'APELLIDO' => $partes[1] ?? '',
    'TALLER' => $taller,
  ];
  // El teléfono debe ir en formato internacional, ej: +573001234567
  if ($telefono) {
    $atributos['SMS'] = $telefono;
  }

  $body = [
    'email' => $email,
    'attributes' => $atributos,
    'updateEnabled' => true,
  ];
  if ($listaId) {
    $body['listIds'] = [$listaId];
  }

  $ch = curl_init('https://api.brevo.com/v3/contacts');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($body),
    CURLOPT_HTTPHEADER => [
      'api-key: ' . BREVO_API_KEY,
      'Content-Type: application/json',
    ],
  ]);
  curl_exec($ch);
  curl_close($ch);
}

// Envía el correo de bienvenida con el link de confirmación (usa la API transaccional
// de Brevo, distinta de la de contactos/listas de arriba — no requiere plantilla previa).
function enviarCorreoConfirmacion($nombre, $email, $linkConfirmacion) {
  $primerNombre = explode(' ', trim($nombre))[0];

  $nom = htmlspecialchars($primerNombre);
  $lnk = htmlspecialchars($linkConfirmacion);
  $tests = [
    ['Genealogía', 'Mira tu historia familiar: lealtades invisibles, secretos y cargas heredadas. Su objetivo es que reconozcas qué es tuyo y qué le pertenece a tu linaje.'],
    ['Eneatipos', 'Descubre cuál de los 9 patrones de personalidad rige tus reacciones automáticas. Su objetivo es que te reconozcas sin juicio y sepas desde dónde actúas.'],
    ['Heridas de la infancia', 'Identifica cuál de las 5 heridas (rechazo, abandono, humillación, traición o injusticia) sigue activa. Su objetivo es que entiendas de dónde vienen tus miedos y empieces a sanarlos.'],
    ['Regresando al Vientre', 'Observa tu vínculo con tu madre y con tu padre. Su objetivo es que puedas honrar tu origen y tomar la vida tal como llegó.'],
  ];
  $listaTests = '';
  foreach ($tests as $t) {
    $listaTests .= '<tr><td style="padding:10px 0;border-bottom:1px solid #eadfcd;font-size:14px;line-height:1.5;color:#211A33;">'
      . '<strong style="color:#C2607D;">' . htmlspecialchars($t[0]) . '.</strong> ' . htmlspecialchars($t[1]) . '</td></tr>';
  }

  $html = '
    <div>
      <div style="background:#211A33;border-radius:14px;padding:24px 22px;text-align:center;margin-bottom:22px;">
        <p style="margin:0 0 6px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#F5C343;">Reconoce tu esencia</p>
        <h2 style="margin:0;color:#ffffff;font-size:24px;line-height:1.3;">Hola, ' . $nom . ': ya diste el primer paso de tu proceso</h2>
      </div>
      <p style="color:#211A33;">Gracias por crear tu cuenta en <strong>Aprender a Vivir Contigo</strong>. Confirma tu correo para activarla y continuar con tu camino:</p>
      <p style="text-align:center;margin:24px 0;">
        <a href="' . $lnk . '" style="background:#F5C343;color:#211A33;padding:15px 30px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;">Confirmar mi correo y continuar</a>
      </p>

      <h3 style="color:#211A33;margin:28px 0 8px;font-size:17px;">Qué vas a vivir aquí</h3>
      <p style="color:#211A33;font-size:14px;margin:0 0 10px;">Este es un acompañamiento terapéutico holístico: une la mirada sistémica de las <strong>Constelaciones Familiares</strong> (Bert Hellinger) con la <strong>Aceptología</strong> (Gerardo Schmedling). No se trata de analizarte desde afuera, sino de que te observes con calma, reconozcas lo que repites y tomes tu lugar con más libertad.</p>
      <p style="color:#211A33;font-size:14px;margin:0 0 10px;">Tu proceso empieza con una <strong>evaluación inicial</strong>: una ficha breve y unos tests de autoobservación. Con tus respuestas, tu terapeuta prepara el acompañamiento a tu medida.</p>

      <h3 style="color:#211A33;margin:24px 0 4px;font-size:17px;">Los tests que vas a diligenciar</h3>
      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">' . $listaTests . '</table>
      <p style="color:#6b6478;font-size:12px;margin:12px 0 0;">Son guías de autoobservación: no son un diagnóstico clínico ni una prueba psicométrica. No hay respuestas correctas; responde con lo primero que sientas. Tus respuestas son privadas.</p>

      <p style="text-align:center;margin:26px 0 8px;">
        <a href="' . $lnk . '" style="background:#211A33;color:#F5C343;padding:13px 26px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;">Quiero continuar mi proceso</a>
      </p>
      <p style="color:#211A33;font-size:12px;margin-top:18px;">Si el botón no funciona, copia y pega este enlace en tu navegador:<br>' . $lnk . '</p>
    </div>';

  return enviarCorreo($email, $nombre, 'Confirma tu correo — Aprender a Vivir Contigo', $html);
}
