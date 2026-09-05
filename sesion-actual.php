<?php
require_once __DIR__ . '/sesion.php';
header('Content-Type: application/json');
echo json_encode(['usuario_id' => $_SESSION['usuario_id'] ?? null]);
