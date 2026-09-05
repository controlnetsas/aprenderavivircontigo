<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// --- Validación de firma (x_signature), igual que en la guía anterior ---
$refPayco = $_POST['x_ref_payco'] ?? '';
$transaccionId = $_POST['x_transaction_id'] ?? '';
$valor = $_POST['x_amount'] ?? '';
$moneda = $_POST['x_currency_code'] ?? '';
$firmaRecibida = $_POST['x_signature'] ?? '';

$firmaEsperada = hash(
  'sha256',
  EPAYCO_CUSTOMER_ID . '^' . EPAYCO_PRIVATE_KEY . '^' . $refPayco . '^' . $transaccionId . '^' . $valor . '^' . $moneda
);

if (!hash_equals($firmaEsperada, $firmaRecibida)) {
  http_response_code(400);
  exit('Firma inválida');
}

$invoice = $_POST['x_id_invoice'] ?? '';
$estadoPago = $_POST['x_response'] ?? '';
$pdo = conexionDB();

if ($estadoPago === 'Aceptada') {
  $partes = explode('-', $invoice);

  if ($partes[1] === 'MEM' && isset($partes[2])) {
    $usuarioId = (int)$partes[2];
    $pdo->prepare(
      "UPDATE usuarios SET estado_membresia = 'activa', fecha_inicio_membresia = NOW(),
       fecha_vencimiento = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE id = ?"
    )->execute([$usuarioId]);

  } elseif ($partes[1] === 'CITA' && isset($partes[2])) {
    $citaId = (int)$partes[2];
    $pdo->prepare("UPDATE citas SET estado = 'confirmada', ref_payco = ? WHERE id = ?")
        ->execute([$refPayco, $citaId]);

  } elseif ($partes[1] === 'TALLER' && isset($partes[2])) {
    // Igual que ya funciona hoy con la inscripción a talleres — sin cambios.
  }
}

http_response_code(200);
echo 'OK';
