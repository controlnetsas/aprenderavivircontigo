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
     . '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
     . '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">'
     . '<link rel="stylesheet" href="/assets/css/style.css">'
     . '<style>'
     . 'body{font-family:Lora,Georgia,serif;background:#FDF9F2;color:#211A33;margin:0;line-height:1.6}'
     . 'h1,h2,h3,h4,.etq,.btn,.chip,.marca,.paso-t,button{font-family:Poppins,Arial,sans-serif}'
     . 'h1{font-size:1.8rem;line-height:1.2;margin:.2em 0 .4em}h2{font-size:1.25rem}h3{font-size:1.05rem}'
     . '.barra-sup{background:#fff;border-bottom:1px solid #eadfc9}'
     . '.barra-sup .in{max-width:980px;margin:0 auto;padding:10px 18px;display:flex;align-items:center;gap:12px}'
     . '.barra-sup img{height:40px}.marca{font-weight:600;font-size:15px;line-height:1.15}.marca small{display:block;font-weight:400;color:#C2607D;font-size:12px;font-family:Lora,serif;font-style:italic}'
     . '.wrap{max-width:860px;margin:0 auto;padding:22px 18px 70px}'
     . '.fila{display:flex;gap:10px;flex-wrap:wrap;align-items:center}'
     . '.etq{display:block;font-weight:600;font-size:14px;margin:16px 0 6px}'
     . '.campo{width:100%;padding:12px 14px;border:1.5px solid #d9cfbd;border-radius:12px;font:inherit;font-size:16px;background:#fff;box-sizing:border-box;transition:border-color .15s,box-shadow .15s}'
     . '.campo:focus{outline:none;border-color:#F5C343;box-shadow:0 0 0 3px rgba(245,195,67,.35)}'
     . '.chip{display:inline-block;padding:3px 11px;border-radius:20px;font-size:12px;font-weight:500;background:#f1e6cf;color:#211A33}'
     . '.ok{background:#e4f3e6;border:1px solid #b9dcbf;padding:12px 14px;border-radius:12px;margin:14px 0}'
     . '.err{background:#fbe6ea;border:1px solid #efb9c5;padding:12px 14px;border-radius:12px;margin:14px 0}'
     . '.barra{height:10px;border-radius:8px;background:#eadfc9;overflow:hidden}'
     . '.barra>i{display:block;height:100%;background:linear-gradient(90deg,#C2607D,#F5C343);transition:width .4s}'
     . '.ej{display:flex;gap:12px;align-items:flex-start}'
     . '.ej input{margin-top:5px;width:20px;height:20px;accent-color:#C2607D}'
     . '.mut{color:#6b6478;font-size:13px}'
     . '.card{background:#fff;border-radius:18px;box-shadow:0 6px 24px rgba(33,26,51,.07);padding:22px 22px}'
     . '.tabla{width:100%;border-collapse:collapse;font-size:14px}.tabla td,.tabla th{padding:8px 6px;border-bottom:1px solid #eadfc9;text-align:left;vertical-align:top}'
     . '.hero{background:linear-gradient(135deg,#211A33,#3a2c55);color:#fff;border-radius:20px;padding:26px 24px;margin:6px 0 20px}'
     . '.hero h1{color:#fff;margin-top:0}.hero p{color:rgba(255,255,255,.85);margin:.4em 0;font-size:15px}.paso-t{font-size:13px}'
     . '.pasos{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}'
     . '.paso-t{flex:1;min-width:130px;background:rgba(255,255,255,.1);border-radius:12px;padding:9px 12px;font-size:13px;color:#fff;text-decoration:none;display:block}'
     . '.paso-t b{display:block;font-size:11px;font-weight:500;opacity:.7;text-transform:uppercase;letter-spacing:.06em}'
     . '.paso-t.hecho{background:rgba(245,195,67,.22)}.paso-t.hecho b{color:#F5C343;opacity:1}'
     . '.nbadge{display:inline-flex;width:30px;height:30px;border-radius:50%;background:#211A33;color:#fff;align-items:center;justify-content:center;font:600 14px Poppins,sans-serif;margin-right:8px}'
     . '.nbadge.hecho{background:#3f9a5b}'
     . '@media(max-width:600px){h1{font-size:1.45rem}.hero{padding:20px 16px}.card{padding:18px 15px}}'
     . '@media print{.barra-sup,.no-imprimir{display:none}body{background:#fff}.card{box-shadow:none}}'
     . '</style></head><body>'
     . '<div class="barra-sup no-imprimir"><div class="in"><a href="/index.html"><img src="/assets/img/logo.png" alt=""></a>'
     . '<span class="marca">Aprender a Vivir Contigo<small>Reconoce tu esencia</small></span></div></div>'
     . '<div class="wrap">';
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

// Partes que el terapeuta puede elegir para una evaluación inicial.
function partesEvaluacionCatalogo() {
  return ['genealogia' => 'Genealogía', 'eneatipo' => 'Eneatipos', 'herida' => 'Heridas de la infancia', 'vientre' => 'Regresando al Vientre'];
}
// Partes elegidas (de un enlace o de una evaluación). Si no hay dato, las tres de siempre.
function partesDeEvaluacion($fila) {
  $validas = array_keys(partesEvaluacionCatalogo());
  $out = [];
  foreach (explode(',', (string)($fila['tests'] ?? '')) as $t) { $t = trim($t); if (in_array($t, $validas, true)) $out[] = $t; }
  $out = array_values(array_unique($out));
  return $out ?: ['genealogia', 'eneatipo', 'herida'];
}
// Test (página) que corresponde a cada parte.
function testDeParte($parte) { return ($parte === 'eneatipo' || $parte === 'herida') ? 'reconocete' : $parte; }

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
  if ($evalU = evaluacionDeUsuario($pdo, $usuario['id'])) {
    foreach (partesDeEvaluacion($evalU) as $pt) $ok[] = testDeParte($pt);
  }
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
  $out = ['genealogia' => false, 'eneatipo' => false, 'herida' => false, 'vientre' => false];
  try {
    $s = $pdo->prepare("SELECT test, datos FROM resultados_tests WHERE usuario_id = ?");
    $s->execute([$usuarioId]);
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
      if ($r['test'] === 'genealogia') $out['genealogia'] = true;
      if ($r['test'] === 'vientre') $out['vientre'] = true;
      if ($r['test'] === 'reconocete') {
        if (strpos((string)$r['datos'], '"parte":"eneatipo"') !== false) $out['eneatipo'] = true;
        if (strpos((string)$r['datos'], '"parte":"herida"') !== false) $out['herida'] = true;
      }
    }
  } catch (Throwable $e) {}
  return $out;
}
