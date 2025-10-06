<?php
// Extensiones permitidas y tamaño máximo (10 MB)
$ALLOWED_EXTS = ['pdf','doc','docx','xls','xlsx','ppt','pptx','png','jpg','jpeg'];
$MAX_BYTES     = 10 * 1024 * 1024;

function clean_name($name){
  $n = iconv('UTF-8','ASCII//TRANSLIT',$name);
  $n = preg_replace('/[^A-Za-z0-9_\.\-]/','_', $n);
  return preg_replace('/_+/','_', $n);
}

function validate_upload(array $file, array $allowedExts, int $maxBytes){
  if(!isset($file) || !is_array($file)) return [false, "No se recibió archivo."];
  if($file['error'] !== UPLOAD_ERR_OK){
    $map = [
      UPLOAD_ERR_INI_SIZE=>"Archivo excede el límite del servidor.",
      UPLOAD_ERR_FORM_SIZE=>"Archivo excede el límite del formulario.",
      UPLOAD_ERR_PARTIAL=>"Subida incompleta.",
      UPLOAD_ERR_NO_FILE=>"No se seleccionó archivo.",
      UPLOAD_ERR_NO_TMP_DIR=>"Falta directorio temporal.",
      UPLOAD_ERR_CANT_WRITE=>"No se pudo escribir en disco.",
      UPLOAD_ERR_EXTENSION=>"Extensión bloqueada por el servidor."
    ];
    return [false, $map[$file['error']] ?? "Error desconocido al subir."];
  }
  $clean    = clean_name($file['name']);
  $ext      = strtolower(pathinfo($clean, PATHINFO_EXTENSION));
  if(!in_array($ext, $allowedExts, true)){
    return [false, "Tipo de archivo no permitido ($ext). Permitidos: ".strtoupper(implode(', ', $allowedExts))."."];
  }
  if($file['size'] > $maxBytes){
    return [false, "Archivo demasiado grande. Máximo 10 MB."];
  }
  return [true, $clean];
}
