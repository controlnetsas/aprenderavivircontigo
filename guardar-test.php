<?php
// guardar-test.php — recibe (JSON) el resultado de un test que la persona decidió guardar.
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
header('Content-Type: application/json; charset=utf-8');
requiereLoginApi();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Método no permitido']); exit; }

$enviado = $_SERVER['HTTP_X_CSRF'] ?? '';
if (empty($_SESSION['csrf']) || !is_string($enviado) || !hash_equals($_SESSION['csrf'], $enviado)) {
  http_response_code(403); echo json_encode(['error' => 'Sesión expirada. Recarga la página.']); exit;
}

$raw = file_get_contents('php://input', false, null, 0, 300000);
$in = json_decode((string)$raw, true);
if (!is_array($in)) { http_response_code(400); echo json_encode(['error' => 'Datos no válidos']); exit; }

$test = $in['test'] ?? '';
if (!is_string($test) || !isset(testsCatalogo()[$test])) { http_response_code(400); echo json_encode(['error' => 'Test no válido']); exit; }

$pdo = conexionDB();
$usuario = usuarioActual();
if (!$usuario || !accesoTest($pdo, $usuario, $test)) { http_response_code(403); echo json_encode(['error' => 'Sin acceso a este test']); exit; }

$resumen = mb_substr(trim((string)($in['resumen'] ?? '')), 0, 2000);
$datos = json_encode($in['datos'] ?? new stdClass(), JSON_UNESCAPED_UNICODE);
if ($resumen === '' || $datos === false || strlen($datos) > 150000) { http_response_code(400); echo json_encode(['error' => 'Resultado vacío o demasiado grande']); exit; }

// Límite sencillo contra abuso: 20 guardados por día.
$n = $pdo->prepare("SELECT COUNT(*) FROM resultados_tests WHERE usuario_id = ? AND creado_en > (NOW() - INTERVAL 1 DAY)");
$n->execute([$usuario['id']]);
if ((int)$n->fetchColumn() >= 20) { http_response_code(429); echo json_encode(['error' => 'Demasiados guardados hoy. Intenta mañana.']); exit; }

$consultaId = null;
foreach (consultasDeUsuario($pdo, $usuario) as $c) { if (($c['estado'] ?? '') !== 'finalizada') { $consultaId = (int)$c['id']; break; } }

$pdo->prepare("INSERT INTO resultados_tests (usuario_id, consulta_id, test, resumen, datos) VALUES (?, ?, ?, ?, ?)")
    ->execute([$usuario['id'], $consultaId, $test, $resumen, $datos]);
echo json_encode(['ok' => true]);
