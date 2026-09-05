<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$pdo = conexionDB();
$stmt = $pdo->query("SELECT id, nombre, especialidad FROM terapeutas ORDER BY nombre");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
