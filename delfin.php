<?php
$SECCION="delfin"; $docDir="documentos/"; $entDir="entregados/"; require_once 'upload_rules.php';
if($_SERVER["REQUEST_METHOD"]==="POST" && isset($_FILES["archivoAlumno"])){
  [$ok,$res]=validate_upload($_FILES["archivoAlumno"],$ALLOWED_EXTS,$MAX_BYTES);
  if($ok){ $dest=$entDir.$SECCION; if(!is_dir($dest)){mkdir($dest,0777,true);}
    $moved=move_uploaded_file($_FILES["archivoAlumno"]["tmp_name"],$dest."/".basename($res));
    $mensaje=$moved?"📤 Documento enviado correctamente.":"❌ No se pudo guardar el archivo."; if(!$moved)$esError=true;
  } else { $mensaje="❌ ".$res; $esError=true; }
}
if(isset($_GET['eliminarAlumno'])){ $f=$_GET['eliminarAlumno']; $ruta=$entDir.$SECCION."/".$f; if(file_exists($ruta)){unlink($ruta); $mensaje="🗑️ Documento eliminado.";} }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="view-transition" content="same-origin">
<title>Nombre de la sección</title>

<!-- Tipografía Montserrat -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="styles.css?v=12">

</head>
<body>

<?php $active='delfin'; include 'nav.php'; ?>

<main class="page"><h1>📁 Delfín</h1>
<?php if(isset($mensaje)): ?><p class="mensaje <?php echo !empty($esError)?'error':''; ?>"><?php echo htmlspecialchars($mensaje); ?></p><?php endif; ?>

<div class="grid-secciones">
  <div class="cuadro"><h3>Documentos disponibles</h3><ul>
  <?php $src=$docDir.$SECCION; if(is_dir($src)){ foreach(array_diff(scandir($src),['.','..']) as $f){ $ruta=$src.'/'.$f; if(is_file($ruta)){ $fecha=date("Y-m-d H:i",filemtime($ruta));
    echo "<li><div class='file-left'><a class='name' href='".htmlspecialchars($ruta,ENT_QUOTES,'UTF-8')."' download>".htmlspecialchars($f)."</a><span class='fecha'>$fecha</span></div></li>"; }}}
    else { echo "<li><div class='file-left'><span class='name'>No hay documentos aún</span></div></li>"; } ?>
  </ul></div>

  <div class="cuadro"><h3>Mis envíos</h3><ul>
  <?php $src=$entDir.$SECCION; if(is_dir($src)){ foreach(array_diff(scandir($src),['.','..']) as $f){ $ruta=$src.'/'.$f; if(is_file($ruta)){ $fecha=date("Y-m-d H:i",filemtime($ruta));
    echo "<li><div class='file-left'><span class='name'>".htmlspecialchars($f)."</span><span class='fecha'>$fecha</span></div><a class='eliminar' href='?eliminarAlumno=".rawurlencode($f)."'>Eliminar</a></li>"; }}}
    else { echo "<li><div class='file-left'><span class='name'>Aún no has enviado archivos</span></div></li>"; } ?>
  </ul>
  <strong>Enviar documento</strong>
  <form class="upload-box <?php echo !empty($esError)?'upload-error':''; ?>" method="POST" enctype="multipart/form-data">
    <div class="upload-label">Selecciona un archivo</div>
    <input type="file" name="archivoAlumno" required>
    <button type="submit">📤 Enviar Documento</button>
    <p class="help-text">Formatos: PDF, DOCX, XLSX, PPTX, PNG, JPG. Máx: 10&nbsp;MB.</p>
  </form></div>
</div>
</main>

</div></body></html>
