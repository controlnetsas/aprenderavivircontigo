<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$terapeutaId = (int)($_GET['terapeuta_id'] ?? 0);
$fecha = $_GET['fecha'] ?? date('Y-m-d');
if (!$terapeutaId || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
  echo json_encode([]);
  exit;
}
$diaSemana = date('w', strtotime($fecha));
$pdo = conexionDB();

$stmt = $pdo->prepare(
  "SELECT hora_inicio, hora_fin, duracion_sesion_min FROM disponibilidad_terapeuta
   WHERE terapeuta_id = ? AND dia_semana = ?"
);
$stmt->execute([$terapeutaId, $diaSemana]);
$bloques = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare(
  "SELECT fecha_hora FROM citas
   WHERE terapeuta_id = ? AND DATE(fecha_hora) = ?
     AND (estado = 'confirmada' OR (estado = 'pendiente_pago' AND reservado_hasta > NOW()))"
);
$stmt->execute([$terapeutaId, $fecha]);
$ocupadas = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'fecha_hora');

$franjas = [];
foreach ($bloques as $b) {
  $actual = strtotime("$fecha {$b['hora_inicio']}");
  $fin = strtotime("$fecha {$b['hora_fin']}");
  $duracion = max(1, (int)$b['duracion_sesion_min']) * 60;
  while ($actual + $duracion <= $fin) {
    $horaTexto = date('Y-m-d H:i:s', $actual);
    if (!in_array($horaTexto, $ocupadas, true)) $franjas[] = date('H:i', $actual);
    $actual += $duracion;
  }
}
echo json_encode(array_values(array_unique($franjas)));
