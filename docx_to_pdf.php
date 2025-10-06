<?php
// Convierte un DOCX generado a PDF usando LibreOffice y lo sirve para descarga

// -------------- CONFIG --------------
// Ajusta aquí si tu ruta de soffice es distinta:
$SOFFICE = 'C:\Program Files\LibreOffice\program\soffice.exe';
// ------------------------------------

$sec  = $_GET['sec']  ?? '';
$file = $_GET['file'] ?? '';

if(!preg_match('/^(estancia1|estancia2|estadia)$/', $sec)){ http_response_code(400); exit('Sección inválida'); }
if(!preg_match('/^[A-Za-z0-9._\-]+\.docx$/', $file)){ http_response_code(400); exit('Archivo inválido'); }

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'entregados' . DIRECTORY_SEPARATOR . $sec . DIRECTORY_SEPARATOR;
$srcDocx = $baseDir . $file;

if(!file_exists($srcDocx)){ http_response_code(404); exit('No existe el DOCX'); }
if(!file_exists($SOFFICE)){ http_response_code(500); exit('LibreOffice no encontrado. Ajusta la ruta en docx_to_pdf.php'); }

// Ejecutar LibreOffice headless para convertir a PDF
$cmd = '"'.$SOFFICE.'" --headless --convert-to pdf --outdir "'.$baseDir.'" "'.$srcDocx.'"';
exec($cmd, $out, $ret);

$pdfFile = preg_replace('/\.docx$/i', '.pdf', $file);
$pdfPath = $baseDir . $pdfFile;

if($ret !== 0 || !file_exists($pdfPath)){
  http_response_code(500);
  echo "No se pudo convertir a PDF. Código: $ret";
  exit;
}

// Servir el PDF para descarga/visualización
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.$pdfFile.'"');
header('Content-Length: '.filesize($pdfPath));
readfile($pdfPath);
