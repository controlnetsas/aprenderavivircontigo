<?php
require_once __DIR__ . '/config.php';

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
