<?php
// enviar-ejercicios-diarios.php — cron diario (Hostinger → Tareas programadas, p. ej. 7:00 a. m.).
// Envía por correo el ejercicio del día a quienes tienen un plan en proceso.
// WhatsApp no se envía solo: el panel del terapeuta ofrece enlaces wa.me para enviar con un toque.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once __DIR__ . '/camino-nucleo.php';

$pdo = conexionDB();
$bases = basesCatalogo();

$consultas = $pdo->query(
  "SELECT id, nombre, email, plantilla_id, fecha_inicio FROM consultas
   WHERE estado = 'en_proceso' AND fecha_inicio IS NOT NULL AND consentimiento_en IS NOT NULL"
)->fetchAll(PDO::FETCH_ASSOC);

$enviados = 0;
foreach ($consultas as $c) {
  $dia = diaActual($c['fecha_inicio']);
  $q = $pdo->prepare("SELECT * FROM plantilla_ejercicios WHERE plantilla_id = ? ORDER BY dia, id");
  $q->execute([$c['plantilla_id']]);
  $todos = $q->fetchAll(PDO::FETCH_ASSOC);
  if (!$todos) continue;
  $ultimoDia = (int)max(array_column($todos, 'dia'));

  if ($dia > $ultimoDia + 1) { // plan terminado: se cierra solo
    $pdo->prepare("UPDATE consultas SET estado = 'finalizada' WHERE id = ?")->execute([$c['id']]);
    continue;
  }

  foreach ($todos as $x) {
    if ((int)$x['dia'] !== $dia) continue;
    $ya = $pdo->prepare("SELECT 1 FROM envios_ejercicios WHERE consulta_id = ? AND ejercicio_id = ?");
    $ya->execute([$c['id'], $x['id']]);
    if ($ya->fetchColumn()) continue;

    $html = correoEjercicio($c['nombre'], $dia, $ultimoDia, $x['titulo'], $bases[$x['base']] ?? '', $x['duracion_min'], $x['instrucciones'], $x['base'] === 'buteyko');
    if (enviarCorreo($c['email'], $c['nombre'], 'Tu ejercicio de hoy · Día ' . $dia, $html)) {
      $pdo->prepare("INSERT IGNORE INTO envios_ejercicios (consulta_id, ejercicio_id) VALUES (?, ?)")->execute([$c['id'], $x['id']]);
      $enviados++;
    }
  }
}
echo date('c') . " — correos enviados: $enviados\n";
