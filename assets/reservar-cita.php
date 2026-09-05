<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/sesion.php';
requiereLoginApi();
header('Content-Type: application/json');

$usuario = usuarioActual();
$terapeutaId = (int)($_POST['terapeuta_id'] ?? 0);
$fechaHora = $_POST['fecha_hora'] ?? '';

if (!$terapeutaId || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fechaHora)) {
  http_response_code(400);
  echo json_encode(['error' => 'Datos inválidos']);
  exit;
}

$pdo = conexionDB();

// Verifica que la franja siga libre justo antes de reservarla (evita choques de última hora).
$stmt = $pdo->prepare(
  "SELECT COUNT(*) FROM citas WHERE terapeuta_id = ? AND fecha_hora = ?
     AND (estado = 'confirmada' OR (estado = 'pendiente_pago' AND reservado_hasta > NOW()))"
);
$stmt->execute([$terapeutaId, $fechaHora]);
if ((int)$stmt->fetchColumn() > 0) {
  http_response_code(409);
  echo json_encode(['error' => 'Esa hora ya no está disponible, elige otra.']);
  exit;
}

$stmt = $pdo->prepare(
  "INSERT INTO citas (usuario_id, terapeuta_id, fecha_hora, estado, reservado_hasta)
   VALUES (?, ?, ?, 'pendiente_pago', DATE_ADD(NOW(), INTERVAL 10 MINUTE))"
);
$stmt->execute([$usuario['id'], $terapeutaId, $fechaHora]);

echo json_encode(['cita_id' => $pdo->lastInsertId()]);
