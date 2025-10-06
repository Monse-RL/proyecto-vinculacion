<?php
$secciones = ["estancia1", "estancia2", "estadia"];
$docDir = "documentos/";
$entregadosDir = "entregados/";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subirSeccion']) && isset($_FILES["archivo"])) {
    $seccion = $_POST['subirSeccion'];
    $archivo = $_FILES["archivo"]["name"];
    $rutaArchivo = $docDir.$seccion."/".basename($archivo);
    if (!is_dir($docDir.$seccion)) { mkdir($docDir.$seccion, 0777, true); }
    $mensaje = move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaArchivo)
        ? "Documento subido correctamente en $seccion."
        : "Error al subir el documento.";
}

if (isset($_GET['eliminar']) && isset($_GET['seccion'])) {
    $archivoEliminar = $_GET['eliminar'];
    $seccion = $_GET['seccion'];
    $rutaArchivo = $docDir.$seccion."/".$archivoEliminar;
    if(file_exists($rutaArchivo)){
        unlink($rutaArchivo);
        $mensaje = "Documento eliminado correctamente.";
    }
}

if (isset($_GET['descargarAlumno']) && isset($_GET['seccionAlumno'])) {
    $archivoAlumno = $_GET['descargarAlumno'];
    $seccionAlumno = $_GET['seccionAlumno'];
    $ruta = $entregadosDir.$seccionAlumno."/".$archivoAlumno;
    if(file_exists($ruta)){
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($ruta).'"');
        readfile($ruta); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Vinculación</title>
<link rel="stylesheet" href="styles.css?v=3">
</head>
<body>
<header class="topbar">
  <div class="wrap">
    <div class="brand"><span class="dot"></span> Vinculación</div>
    <div class="title">Panel de Vinculación (Admin)</div>
    <div class="actions"><a class="link-btn" href="alumnos.php">Portal Alumnos</a></div>
  </div>
</header>

<main class="container">
<h1>📂 Panel de Vinculación</h1>
<?php if(isset($mensaje)): ?><p class="mensaje"><?=htmlspecialchars($mensaje)?></p><?php endif; ?>

<div class="grid-secciones">
<?php foreach($secciones as $sec): ?>
  <div class="cuadro">
    <h3><?=htmlspecialchars($sec)?></h3>
    <strong>Documentos disponibles:</strong>
    <ul>
      <?php if(is_dir($docDir.$sec)){
        foreach(array_diff(scandir($docDir.$sec),['.','..']) as $archivo){
          $fechaHora=date("Y-m-d H:i", filemtime($docDir.$sec."/".$archivo));
          echo "<li><div class='file-left'><span class='name'>$archivo</span><span class='fecha'>$fechaHora</span></div><a href='?eliminar=$archivo&seccion=$sec' class='eliminar'>Eliminar</a></li>";
        }
      } ?>
    </ul>
    <strong>Subir documento:</strong>
    <form class="upload-box" method="POST" enctype="multipart/form-data">
      <div class="upload-label">Selecciona un archivo</div>
      <input type="file" name="archivo" required>
      <input type="hidden" name="subirSeccion" value="<?=$sec?>">
      <button type="submit">📤 Subir Documento</button>
    </form>
  </div>

  <div class="cuadro">
    <h3><?=htmlspecialchars($sec)?></h3>
    <strong>Documentos enviados por alumnos:</strong>
    <ul>
      <?php if(is_dir($entregadosDir.$sec)){
        foreach(array_diff(scandir($entregadosDir.$sec),['.','..']) as $archivoAlumno){
          $fechaHora=date("Y-m-d H:i", filemtime($entregadosDir.$sec."/".$archivoAlumno));
          echo "<li><div class='file-left'><span class='name'>$archivoAlumno</span><span class='fecha'>$fechaHora</span></div><a href='?descargarAlumno=$archivoAlumno&seccionAlumno=$sec' class='descargar'>Descargar</a></li>";
        }
      } ?>
    </ul>
  </div>
<?php endforeach; ?>
</div>
</main>
</body>
</html>
