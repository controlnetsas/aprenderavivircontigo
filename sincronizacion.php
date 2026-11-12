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

  $html = '
    <div>
      <h2 style="color:#211A33;">Hola, ' . htmlspecialchars($primerNombre) . '</h2>
      <p style="color:#211A33;">Gracias por crear tu cuenta en <strong>Aprender a Vivir Contigo</strong>. Confirma tu correo para activarla:</p>
      <p style="text-align:center;margin:28px 0;">
        <a href="' . htmlspecialchars($linkConfirmacion) . '" style="background:#F5C343;color:#211A33;padding:14px 28px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;">Confirmar mi correo</a>
      </p>
      <p style="color:#211A33;font-size:0.85rem;">Si el botón no funciona, copia y pega este enlace en tu navegador:<br>' . htmlspecialchars($linkConfirmacion) . '</p>
    </div>';

  return enviarCorreo($email, $nombre, 'Confirma tu correo — Aprender a Vivir Contigo', $html);
}
