<?php
require_once __DIR__ . '/db.php';
$pdo = conexionDB();

$pdo->exec(
  "UPDATE usuarios SET estado_membresia = 'vencida'
   WHERE estado_membresia = 'activa' AND fecha_vencimiento < CURDATE()"
);

$pdo->exec(
  "UPDATE citas SET estado = 'cancelada'
   WHERE estado = 'pendiente_pago' AND reservado_hasta < NOW()"
);
