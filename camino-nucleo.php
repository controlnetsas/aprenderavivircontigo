<?php
// camino-nucleo.php — piezas compartidas del módulo de acompañamiento (consultas + Mi camino).
// No inicia sesión: también lo usa el cron diario (línea de comandos).
date_default_timezone_set('America/Bogota');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/correos.php';

const WHATSAPP_AVC = '573176435131';

// Catálogo de talleres que se pueden recomendar. Edita aquí nombres, páginas o bases.
function talleresCatalogo() {
  return [
    'vientre' => [
      'nombre' => 'Regresando al Vientre',
      'pagina' => '/regresando-al-vientre.html',
      'resumen' => 'Un recorrido por la etapa gestacional y el primer vínculo con mamá y papá.',
      'bases' => ['constelaciones', 'aceptologia', 'buteyko'],
    ],
    'linaje' => [
      'nombre' => 'Sanando con mi Linaje',
      'pagina' => '/taller-genealogia.html',
      'resumen' => 'Construyes tu árbol genealógico y trabajas seis temas sistémicos con frases sanadoras.',
      'bases' => ['constelaciones', 'aceptologia'],
    ],
    'reconocete' => [
      'nombre' => 'Reconócete',
      'pagina' => '/reconocete-modulo-3.html',
      'resumen' => 'Tu eneatipo y tus heridas de la infancia, con ejercicios de resignificación.',
      'bases' => ['eneagrama', 'aceptologia'],
    ],
  ];
}

function basesCatalogo() {
  return [
    'aceptologia' => 'Aceptología',
    'constelaciones' => 'Constelaciones Familiares',
    'eneagrama' => 'Eneagrama',
    'buteyko' => 'Respiración Buteyko',
  ];
}

function estadosTaller() {
  return [
    'recomendado' => 'Recomendado',
    'interesado' => 'Me interesa',
    'inscrito' => 'Inscrito',
    'completado' => 'Completado',
  ];
}

const AVISO_RESPIRACION = 'Si tienes una condición cardíaca o respiratoria, epilepsia o estás embarazada, consulta con tu médico antes de practicar ejercicios de respiración. Suspende el ejercicio si sientes mareo.';

const TEXTO_CONSENTIMIENTO = 'Acepto que Aprender a Vivir Contigo guarde mis datos y mi avance en este plan para acompañarme, y que me envíe por correo y WhatsApp los ejercicios y mensajes del plan. Puedo pedir que se detengan los envíos o que borren mis datos escribiendo al +57 317 643 5131.';

function h($s) {
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function hoy() { return date('Y-m-d'); }
function ahora() { return date('Y-m-d H:i:s'); }

// Número de día del plan (1 = primer día). 0 si el plan aún no inicia.
function diaActual($fechaInicio) {
  if (!$fechaInicio) return 0;
  $inicio = new DateTime($fechaInicio);
  $hoyFecha = new DateTime(hoy());
  return (int)$inicio->diff($hoyFecha)->format('%r%a') + 1;
}

function fechaDelDia($fechaInicio, $dia) {
  return date('Y-m-d', strtotime($fechaInicio . ' +' . max(0, $dia - 1) . ' days'));
}

function fechaCorta($fecha) {
  return $fecha ? date('d/m/Y', strtotime($fecha)) : '';
}

function talleresDeConsulta($csv) {
  $validos = array_keys(talleresCatalogo());
  $lista = array_filter(array_map('trim', explode(',', (string)$csv)));
  return array_values(array_intersect($validos, $lista));
}

function primerNombre($nombre) {
  return explode(' ', trim((string)$nombre))[0];
}

function ocultarCorreo($email) {
  $partes = explode('@', (string)$email, 2);
  if (count($partes) !== 2) return '';
  $local = $partes[0];
  $visible = mb_substr($local, 0, min(2, mb_strlen($local)));
  return $visible . '•••@' . $partes[1];
}

function urlInvitacion($token) {
  return SITIO_URL . '/invitacion.php?t=' . $token;
}

// ---- Estructura HTML común de las páginas del módulo ----
function paginaInicio($titulo) {
  header('Content-Type: text/html; charset=utf-8');
  header('X-Robots-Tag: noindex, nofollow');
  header('Referrer-Policy: no-referrer');
  echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
     . '<meta name="viewport" content="width=device-width, initial-scale=1">'
     . '<meta name="robots" content="noindex,nofollow">'
     . '<meta name="theme-color" content="#211A33"><link rel="manifest" href="/manifest.json">'
     . '<title>' . h($titulo) . ' · Aprender a Vivir Contigo</title>'
     . '<link rel="stylesheet" href="/assets/css/style.css">'
     . '<style>'
     . '.wrap{max-width:860px;margin:0 auto;padding:28px 18px 60px}'
     . '.fila{display:flex;gap:10px;flex-wrap:wrap;align-items:center}'
     . '.etq{display:block;font-weight:600;margin:14px 0 6px}'
     . '.campo{width:100%;padding:11px 12px;border:1px solid #d9cfbd;border-radius:10px;font:inherit;background:#fff;box-sizing:border-box}'
     . '.chip{display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;background:#f1e6cf;color:#211A33}'
     . '.ok{background:#e4f3e6;border:1px solid #b9dcbf;padding:12px 14px;border-radius:10px;margin:14px 0}'
     . '.err{background:#fbe6ea;border:1px solid #efb9c5;padding:12px 14px;border-radius:10px;margin:14px 0}'
     . '.barra{height:10px;border-radius:8px;background:#eadfc9;overflow:hidden}'
     . '.barra>i{display:block;height:100%;background:#C2607D}'
     . '.ej{display:flex;gap:12px;align-items:flex-start}'
     . '.ej input{margin-top:5px;width:20px;height:20px;accent-color:#C2607D}'
     . '.mut{color:#6b6478;font-size:13px}'
     . '.tabla{width:100%;border-collapse:collapse;font-size:14px}.tabla td,.tabla th{padding:8px 6px;border-bottom:1px solid #eadfc9;text-align:left;vertical-align:top}'
     . '</style></head><body style="background:#FDF9F2;color:#211A33"><div class="wrap">'
     . '<p><a href="/index.html"><img src="/assets/img/logo.png" alt="Aprender a Vivir Contigo" style="height:42px"></a></p>';
}

function paginaFin() {
  echo '</div></body></html>';
}

function aviso($tipo, $texto) {
  return '<div class="' . ($tipo === 'ok' ? 'ok' : 'err') . '">' . $texto . '</div>';
}

// Enlace "Mi camino" solo si la persona tiene una consulta con plan activo.
function consultasDeUsuario($pdo, $usuario) {
  // Vincula consultas hechas con el mismo correo que aún no tengan cuenta enlazada.
  $v = $pdo->prepare("UPDATE consultas SET usuario_id = ? WHERE usuario_id IS NULL AND email = ?");
  $v->execute([$usuario['id'], $usuario['email']]);
  $s = $pdo->prepare(
    "SELECT c.*, p.nombre AS plantilla_nombre FROM consultas c
     JOIN plantillas_seguimiento p ON p.id = c.plantilla_id
     WHERE c.usuario_id = ? AND c.fecha_inicio IS NOT NULL ORDER BY c.id DESC"
  );
  $s->execute([$usuario['id']]);
  return $s->fetchAll(PDO::FETCH_ASSOC);
}

// ---- Tests privados: solo miembros activos, terapeuta, o consultantes a quienes el terapeuta se los asignó ----
function testsCatalogo() {
  return [
    'genealogia' => ['nombre' => 'Test de Genealogía', 'resumen' => 'Construye tu árbol y recorre los seis temas del taller Sanando con mi Linaje.', 'pagina' => '/test-genealogia.php'],
    'reconocete' => ['nombre' => 'Test Reconóce TE', 'resumen' => 'Los 9 eneatipos y las 5 heridas de la infancia.', 'pagina' => '/test-reconocete.php'],
    'vientre'    => ['nombre' => 'Test Regresando al Vientre', 'resumen' => '30 preguntas sobre tu vínculo con tu madre y con tu padre.', 'pagina' => '/test-vientre.php'],
  ];
}

function testsDeConsulta($csv) {
  $validos = array_keys(testsCatalogo());
  $out = [];
  foreach (explode(',', (string)$csv) as $t) { $t = trim($t); if (in_array($t, $validos, true)) $out[] = $t; }
  return array_values(array_unique($out));
}

// Lista de tests que la persona puede hacer hoy.
function testsPermitidos($pdo, $usuario) {
  $todos = array_keys(testsCatalogo());
  if (!$usuario) return [];
  if (($usuario['rol'] ?? '') === 'terapeuta' || esMiembroActivo($usuario)) return $todos;
  $ok = [];
  if (evaluacionDeUsuario($pdo, $usuario['id'])) $ok = ['genealogia', 'reconocete'];
  foreach (consultasDeUsuario($pdo, $usuario) as $c) {
    if (($c['estado'] ?? '') === 'finalizada') continue;
    $ok = array_merge($ok, testsDeConsulta($c['tests_asignados'] ?? ''));
  }
  return array_values(array_unique($ok));
}

function accesoTest($pdo, $usuario, $clave) {
  return in_array($clave, testsPermitidos($pdo, $usuario), true);
}

// Página privada de un test: exige sesión y acceso; si no, muestra cómo conseguirlo.
function abrirTest($clave) {
  requiereLogin();
  $pdo = conexionDB();
  $usuario = usuarioActual();
  if (!$usuario) { header('Location: /login.html'); exit; }
  $cat = testsCatalogo()[$clave];
  if (!accesoTest($pdo, $usuario, $clave)) {
    paginaInicio($cat['nombre']);
    echo '<div class="card"><h1>' . h($cat['nombre']) . '</h1>'
       . '<p>Este test se hace con acompañamiento. Está disponible para miembros del club o cuando Carlos te lo envía después de tu consulta.</p>'
       . '<p class="fila"><a class="btn btn-primary" target="_blank" rel="noopener" href="https://wa.me/' . WHATSAPP_AVC . '?text=' . rawurlencode('Hola Carlos, quiero hacer el ' . $cat['nombre'] . '.') . '">Pedirlo por WhatsApp</a>'
       . '<a class="btn btn-outline-dark" href="/membresia.php">Mi membresía</a></p></div>';
    paginaFin();
    exit;
  }
  return [$pdo, $usuario, $cat];
}

const TEXTO_CONSENTIMIENTO_EVAL = 'Acepto que Aprender a Vivir Contigo guarde mis datos, mis respuestas y los resultados de mis tests, y que el terapeuta los vea y reciba un informe con ellos para acompañarme. Puedo pedir que borren mis datos escribiendo al +57 317 643 5131.';

// Evaluación inicial (por enlace del terapeuta) de la persona, si la tiene.
function evaluacionDeUsuario($pdo, $usuarioId) {
  try {
    $s = $pdo->prepare("SELECT * FROM evaluaciones WHERE usuario_id = ?");
    $s->execute([$usuarioId]);
    return $s->fetch(PDO::FETCH_ASSOC) ?: null;
  } catch (Throwable $e) { return null; }
}

// ¿Ya guardó cada parte de los tests de la evaluación? (genealogía, eneatipo, heridas)
function avanceTestsEvaluacion($pdo, $usuarioId) {
  $out = ['genealogia' => false, 'eneatipo' => false, 'herida' => false];
  try {
    $s = $pdo->prepare("SELECT test, datos FROM resultados_tests WHERE usuario_id = ?");
    $s->execute([$usuarioId]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
      if ($r['test'] === 'genealogia') $out['genealogia'] = true;
      if ($r['test'] === 'reconocete') {
        if (strpos((string)$r['datos'], '"parte":"eneatipo"') !== false) $out['eneatipo'] = true;
        if (strpos((string)$r['datos'], '"parte":"herida"') !== false) $out['herida'] = true;
      }
    }
  } catch (Throwable $e) {}
  return $out;
}
