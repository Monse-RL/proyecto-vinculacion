<?php
session_start();

// Proteger acceso: solo alumnos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "alumno") {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Portal del Alumno</title>
<link rel="stylesheet" href="styles.css?v=21">
<style>
  body { font-family:'Montserrat', sans-serif; background:#f8f9fa; margin:0; }
  header {
    background:#7c0d2e; padding:15px 20px; color:#fff;
    display:flex; justify-content:space-between; align-items:center;
  }
  header h1 { margin:0; font-size:20px; }
  header a {
    color:#fff; text-decoration:none; background:#a8324e;
    padding:8px 14px; border-radius:8px; font-size:14px;
  }
  .container { display:flex; min-height:100vh; }
  .sidebar {
    width:250px; background:#fff; padding:20px;
    border-right:1px solid #ddd;
    display:flex; flex-direction:column; justify-content:space-between;
  }
  .sidebar h2 { font-size:18px; color:#333; margin-bottom:15px; }
  .sidebar ul { list-style:none; padding:0; }
  .sidebar li {
    margin-bottom:12px;
  }
  .sidebar a {
    display:block; padding:10px; border-radius:8px;
    text-decoration:none; font-weight:500;
    background:#f1f1f1; color:#444;
  }
  .sidebar a:hover {
    background:#7c0d2e; color:#fff;
  }
  .logout-small {
    margin-top:20px;
    text-align:center;
  }
  .logout-small a {
    display:inline-block; font-size:12px; padding:6px 10px;
    background:#b02a37; color:#fff; text-decoration:none;
    border-radius:6px;
  }
  .logout-small a:hover {
    background:#7c0d2e;
  }
  .content {
    flex:1; padding:40px;
  }
  .bienvenida {
    background:#fff; padding:30px; border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
    text-align:center;
  }
  .bienvenida h2 { margin:0 0 15px; color:#333; }
</style>
</head>
<body>
<header>
  <h1>📂 Gestor de Documentos - Alumno</h1>
  <a href="logout.php">Cerrar Sesión</a>
</header>

<div class="container">
  <nav class="sidebar">
    <div>
      <h2>Secciones</h2>
      <ul>
        <li><a href="estancia1.php">📁 Estancia 1</a></li>
        <li><a href="estancia2.php">📁 Estancia 2</a></li>
        <li><a href="estadia.php">📁 Estadia</a></li>
        <li><a href="delfin.php">📁 Delfín</a></li>
        <li><a href="conocete.php">📁 Conócete</a></li>
      </ul>
    </div>
    <div class="logout-small">
      <a href="logout.php">⏻ Cerrar</a>
    </div>
  </nav>

  <main class="content">
    <div class="bienvenida">
      <h2>Bienvenido, <?= htmlspecialchars($usuario) ?> 👋</h2>
      <p>Selecciona en el menú lateral el trámite que deseas realizar.</p>
    </div>
  </main>
</div>
</body>
</html>
