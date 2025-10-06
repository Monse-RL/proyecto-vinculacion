<?php
// Guarda un DOCX en entregados/<seccion>/ usando templates/FORMATO.docx y abre vista previa

session_start();

// -------- Config --------
$BASE_ENT = __DIR__ . '/entregados/';

// Helpers
function raw($k){ return trim($_POST[$k] ?? ''); }

// Validar sección de destino
$SECCION = preg_replace('/[^a-z0-9_]/i','', $_POST['seccion_actual'] ?? '');
if (!$SECCION || !in_array($SECCION, ['estancia1','estancia2','estadia'], true)) {
  http_response_code(400);
  exit('Sección inválida.');
}

// Composer autoload y PHPWord
require_once __DIR__ . '/vendor/autoload.php';
if (!class_exists(\PhpOffice\PhpWord\TemplateProcessor::class)) {
  http_response_code(500);
  exit('PHPWord no encontrado. Ejecuta: composer require phpoffice/phpword');
}
use PhpOffice\PhpWord\TemplateProcessor;

// Plantilla
$templatePath = __DIR__ . '/templates/FORMATO.docx';
if (!file_exists($templatePath)) {
  http_response_code(500);
  exit('No se encontró la plantilla: templates/FORMATO.docx');
}

// Datos del formulario
$replacements = [
  'solicitud_de'     => raw('solicitud_de'),
  'periodo'          => raw('periodo'),
  'recurse'          => empty($_POST['recurse']) ? 'No' : 'Sí',

  'alumno_nombre'    => raw('alumno_nombre'),
  'alumno_sexo'      => raw('alumno_sexo'),
  'cuatrimestre'     => raw('cuatrimestre'),
  'matricula'        => raw('matricula'),
  'carrera'          => raw('carrera'),
  'tel_alumno'       => raw('tel_alumno'),
  'mail_alumno'      => raw('mail_alumno'),

  'org_nombre'       => raw('org_nombre'),
  'destinatario'     => raw('destinatario'),
  'org_tel'          => raw('org_tel'),
  'dest_mail'        => raw('dest_mail'),
  'asesor_academico' => raw('asesor_academico'),

  'observaciones'    => raw('observaciones'),
];

try {
  // Generar DOCX
  $tp = new TemplateProcessor($templatePath);
  foreach ($replacements as $k => $v) { $tp->setValue($k, $v === '' ? '—' : $v); }

  // Carpeta de destino
  $destDir = $BASE_ENT . $SECCION;
  if (!is_dir($destDir)) { mkdir($destDir, 0777, true); }

  // Nombre (preferir matrícula)
  $baseId  = $replacements['matricula'] !== '' ? $replacements['matricula'] : ($replacements['alumno_nombre'] ?: 'Alumno');
  $slugId  = preg_replace('/[^A-Za-z0-9\-]+/', '_', $baseId);
  $file    = "Solicitud_{$slugId}_" . ucfirst($SECCION) . '_' . date('Ymd_His') . ".docx";
  $save    = $destDir . '/' . $file;

  $tp->saveAs($save);

  // Guardar datos en sesión para la vista previa
  $_SESSION['solicitud_preview'] = [
    'seccion' => $SECCION,
    'file'    => $file,                 // nombre del archivo guardado
    'vals'    => $replacements,         // valores capturados
    'fecha'   => date('d/m/Y'),
  ];

  // Ir a la vista previa
  header("Location: vista_solicitud.php");
  exit;

} catch (Throwable $e) {
  http_response_code(500);
  echo "Error al generar el documento: " . htmlspecialchars($e->getMessage());
}
