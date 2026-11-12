<?php
// informe.php — ver el informe de una evaluación (terapeuta responsable o la propia persona).
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/camino-nucleo.php';
requiereLogin();
$pdo = conexionDB();
$usuario = usuarioActual();
$s = $pdo->prepare("SELECT e.*, u.nombre AS persona FROM evaluaciones e JOIN usuarios u ON u.id = e.usuario_id WHERE e.id = ?");
$s->execute([(int)($_GET['id'] ?? 0)]);
$ev = $s->fetch(PDO::FETCH_ASSOC);
$permitido = $ev && $usuario && ((int)$ev['usuario_id'] === (int)$usuario['id'] || (($usuario['rol'] ?? '') === 'terapeuta' && (int)$ev['terapeuta_usuario_id'] === (int)$usuario['id']));
if (!$permitido) { http_response_code(404); paginaInicio('No encontrado'); echo '<div class="card"><p>No encontramos ese informe.</p></div>'; paginaFin(); exit; }
paginaInicio('Informe de ' . $ev['persona']);
echo '<p class="mut">' . (($usuario['rol'] ?? '') === 'terapeuta' ? '<a href="/evaluaciones.php">← Evaluaciones</a>' : '<a href="/mi-evaluacion.php">← Mi evaluación</a>') . ' · <a href="#" onclick="window.print();return false">Imprimir / guardar PDF</a></p>';
echo '<div class="card">' . ($ev['informe'] ?: '<p>El informe aún no se ha generado: la persona no ha terminado su evaluación.</p>') . '</div>';
paginaFin();
