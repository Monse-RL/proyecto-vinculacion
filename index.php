<?php
session_start();

// --- Usuarios de ejemplo ---
$alumnos = [
    "20230001" => "20230001",
    "20230002" => "20230002"
];
$personal = [
    "admin@vinculacion.com" => "admin123"
];

// --- Directorios ---
$docDir = "documentos/";

// --- Cerrar sesión ---
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// --- Login ---
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $pass    = trim($_POST['password']);
    $tipo    = $_POST['tipo'] ?? '';

    if ($tipo === "alumno") {
        if (isset($alumnos[$usuario]) && $alumnos[$usuario] === $pass) {
            $_SESSION['rol'] = "alumno";
            $_SESSION['usuario'] = $usuario;
        } else {
            $mensaje = "❌ Matrícula o contraseña incorrecta.";
        }
    } elseif ($tipo === "personal") {
        if (isset($personal[$usuario]) && $personal[$usuario] === $pass) {
            $_SESSION['rol'] = "personal";
            $_SESSION['usuario'] = $usuario;
        } else {
            $mensaje = "❌ Correo o contraseña incorrectos.";
        }
    } else {
        $mensaje = "❌ Debes seleccionar un tipo de usuario.";
    }
}

// --- Sección para alumno ---
$seccion = $_GET['seccion'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestor de Documentos</title>
<link rel="stylesheet" href="styles.css?v=55">
<style>
/* ----------------- BODY ----------------- */
body {
    margin: 0;
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #74ebd5, #ACB6E5);
    min-height: 100vh;
    color: #333;
}

/* ----------------- LOGIN ----------------- */
.login-container {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; min-height: 100vh;
    background: rgba(255,255,255,0.95); padding: 40px;
    border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    animation: fadeIn 1s ease-out;
}
.login-container h1 {
    margin-bottom: 25px; color: #333;
    animation: slideDown 0.8s ease-out;
}
.login-container .form-group { width: 100%; margin-bottom: 15px; }
.login-container label { font-weight: 600; margin-bottom: 5px; display: block; color: #555; }
.login-container input, .login-container select {
    width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ccc;
    transition: all 0.3s ease;
}
.login-container input:focus, .login-container select:focus {
    border-color: #3a0ca3; box-shadow: 0 0 10px rgba(58,12,163,0.3); outline: none;
}
.login-btn {
    width: 100%; padding: 14px; border: none; border-radius: 10px;
    background: #3a0ca3; color: #fff; font-size: 16px; cursor: pointer;
    transition: transform 0.2s ease, background 0.3s ease;
}
.login-btn:hover { background: #7209b7; transform: translateY(-2px); }
.mensaje { color: #b00020; font-weight: 600; text-align: center; margin-bottom: 15px; animation: shake 0.5s; }

/* ----------------- HEADER ----------------- */
header {
    background:#3a0ca3; padding:15px 20px; color:#fff;
    display:flex; justify-content:space-between; align-items:center;
    position: relative;
}
header h1 { margin:0; font-size:20px; }
.menu-btn { background:none; border:none; color:white; font-size:24px; cursor:pointer; }
.menu-opciones {
    display:none; position:absolute; top:60px; right:20px;
    background:#fff; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.2);
    overflow:hidden; z-index:1000;
}
.menu-opciones form, .menu-opciones a {
    display:block; padding:12px 20px; text-align:left;
    background:#fff; border:none; width:100%; cursor:pointer;
    text-decoration:none; color:#333; transition:0.2s;
}
.menu-opciones a:hover, .menu-opciones form button:hover { background:#f1f1f1; }

/* ----------------- MAIN ----------------- */
.container { display:flex; min-height:100vh; justify-content:center; padding:40px 20px; }
.content { flex:1; max-width:1100px; }

/* ----------------- DOCS ----------------- */
.docs-section {
    background:#fff; border-radius:16px; padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1); margin-top:25px;
    animation: fadeInUp 0.8s ease-out;
}
.docs-section h3 { color:#3a0ca3; margin-bottom:15px; }
.doc-list { list-style:none; padding:0; display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:15px; }
.doc-list li {
    padding:15px 20px; border-radius:12px;
    box-shadow:0 3px 8px rgba(0,0,0,0.08); transition: all 0.3s;
    font-weight:600; display:flex; align-items:center; justify-content:space-between;
}
.doc-list li:hover { transform: translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
.doc-list a { text-decoration:none; color:#fff; flex:1; }

/* Colores por tipo de documento */
.doc-list li:nth-child(1) { background: linear-gradient(135deg,#6dd5ed,#2193b0);}
.doc-list li:nth-child(2) { background: linear-gradient(135deg,#fbc2eb,#a6c1ee);}
.doc-list li:nth-child(3) { background: linear-gradient(135deg,#fda085,#f6d365);}
.doc-list li:nth-child(4) { background: linear-gradient(135deg,#a1c4fd,#c2e9fb);}
.doc-list li:nth-child(5) { background: linear-gradient(135deg,#ffecd2,#fcb69f);}
.doc-list li:nth-child(6) { background: linear-gradient(135deg,#f093fb,#f5576c);}

/* ----------------- BACK BUTTON ----------------- */
.back-btn {
    display:inline-block; margin-top:25px;
    background:#f72585; color:#fff;
    padding:12px 20px; border-radius:12px;
    text-decoration:none; font-weight:bold; transition:0.3s;
}
.back-btn:hover { background:#b5179e; }

/* ----------------- TRAMITES ----------------- */
.tarjeta {
    display:inline-block; background:#fff; padding:20px; border-radius:16px;
    margin:10px; text-align:center; width:180px; transition:0.3s;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    animation: fadeInUp 0.6s ease-out;
}
.tarjeta:hover { transform: translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.2); }
.tarjeta div { font-size:40px; margin-bottom:10px; }

/* ----------------- FORMULARIO GENERAR SOLICITUD ----------------- */
.form-section {
    background:#fff; border-radius:16px; padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1); margin-top:25px;
    animation: fadeInUp 0.8s ease-out;
}
.form-section h3 { color:#3a0ca3; margin-bottom:20px; }
.form-group { margin-bottom:20px; }
.form-group label { display:block; margin-bottom:8px; font-weight:600; color:#555; }
.form-group input, .form-group select, .form-group textarea {
    width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;
    font-family:inherit; font-size:14px;
    transition:border 0.3s ease;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color:#3a0ca3; outline:none;
}
.form-row { display:flex; gap:15px; margin-bottom:15px; }
.form-row .form-group { flex:1; }
.radio-group { display:flex; gap:15px; align-items:center; }
.radio-group label { display:flex; align-items:center; gap:5px; font-weight:normal; }
.radio-group input[type="radio"] { width:auto; }
.checkbox-group { display:flex; align-items:center; gap:8px; }
.checkbox-group input[type="checkbox"] { width:auto; }
.form-actions { display:flex; gap:15px; margin-top:25px; }
.btn { padding:12px 25px; border:none; border-radius:8px; cursor:pointer;
    font-weight:600; transition:all 0.3s ease; }
.btn-cancel { background:#6c757d; color:white; }
.btn-cancel:hover { background:#5a6268; }
.btn-generate { background:#3a0ca3; color:white; }
.btn-generate:hover { background:#7209b7; }
.divider { height:1px; background:#eee; margin:25px 0; }
.subtitle { font-weight:600; margin-bottom:15px; color:#3a0ca3; }

/* ----------------- PANEL VINCULACIÓN ----------------- */
.panel-vinculacion { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:25px; }
.estadistica-card {
    background:#fff; border-radius:16px; padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1); text-align:center;
    animation: fadeInUp 0.8s ease-out;
}
.estadistica-card h3 { color:#3a0ca3; margin-bottom:15px; font-size:18px; }
.estadistica-valor { font-size:42px; font-weight:700; color:#3a0ca3; margin:15px 0; }
.estadistica-desc { color:#666; font-size:14px; }
.estadistica-card.estancias { background:linear-gradient(135deg,#a1c4fd,#c2e9fb); }
.estadistica-card.beneficiarios { background:linear-gradient(135deg,#ffecd2,#fcb69f); }
.estadistica-card.estadias { background:linear-gradient(135deg,#fbc2eb,#a6c1ee); }

.tramite-section {
    background:#fff; border-radius:16px; padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1); margin-top:25px;
    animation: fadeInUp 0.8s ease-out;
}
.tramite-section h3 { color:#3a0ca3; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
.upload-area {
    border:2px dashed #ddd; border-radius:12px; padding:30px;
    text-align:center; margin-bottom:20px; transition:0.3s;
}
.upload-area:hover { border-color:#3a0ca3; background:#f8f9ff; }
.upload-area p { margin:10px 0; color:#666; }
.upload-btn {
    background:#3a0ca3; color:white; border:none;
    padding:10px 20px; border-radius:8px; cursor:pointer;
    transition:0.3s; font-weight:600;
}
.upload-btn:hover { background:#7209b7; }
.documentos-table { width:100%; border-collapse:collapse; margin-top:15px; }
.documentos-table th, .documentos-table td {
    padding:12px 15px; text-align:left; border-bottom:1px solid #eee;
}
.documentos-table th { background:#f8f9fa; color:#3a0ca3; font-weight:600; }
.documentos-table tr:hover { background:#f8f9ff; }
.badge { padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600; }
.badge-pendiente { background:#fff3cd; color:#856404; }
.badge-aprobado { background:#d1edff; color:#004085; }
.badge-rechazado { background:#f8d7da; color:#721c24; }

/* ----------------- ANIMACIONES ----------------- */
@keyframes fadeIn { from {opacity:0;} to{opacity:1;} }
@keyframes slideDown { from {transform: translateY(-20px); opacity:0;} to {transform:translateY(0); opacity:1;} }
@keyframes fadeInUp { from {transform: translateY(20px); opacity:0;} to {transform:translateY(0); opacity:1;} }
@keyframes shake { 0%,100%{transform:translateX(0);} 20%,60%{transform:translateX(-5px);} 40%,80%{transform:translateX(5px);} }
</style>
</head>
<body>

<?php if (!isset($_SESSION['rol'])): ?>
  <div class="login-container">
    <h1>🔑 Acceso</h1>
    <?php if($mensaje): ?><p class="mensaje"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label for="tipo">Tipo de usuario</label>
        <select name="tipo" id="tipo" required>
          <option value="">-- Selecciona --</option>
          <option value="alumno">Alumno</option>
          <option value="personal">Personal Vinculación</option>
        </select>
      </div>
      <div class="form-group">
        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" id="usuario" placeholder="Matrícula o Correo" required>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="Contraseña" required>
      </div>
      <button type="submit" name="login" class="login-btn">Ingresar</button>
    </form>
  </div>

<?php else: ?>
  <header>
      <h1>📂 Gestor de Documentos - <?= $_SESSION['rol'] === 'alumno' ? 'Alumno' : 'Personal Vinculación' ?></h1>
      <button class="menu-btn" onclick="toggleMenu()">☰</button>
      <div class="menu-opciones" id="menuOpciones">
        <form method="POST"><button type="submit" name="logout">Cerrar Sesión</button></form>
        <a href="?">Volver al menú principal</a>
      </div>
  </header>

  <div class="container">
    <main class="content">
      <?php if ($_SESSION['rol'] === 'personal'): ?>
        <!-- PANEL DE VINCULACIÓN -->
        <h2>🏢 Panel de Vinculación</h2>
        
        <!-- Estadísticas -->
        <div class="panel-vinculacion">
          <div class="estadistica-card estancias">
            <h3>📊 ESTANCIAS</h3>
            <div class="estadistica-valor">1000</div>
            <div class="estadistica-desc">Total de estancias registradas</div>
          </div>
          <div class="estadistica-card beneficiarios">
            <h3>👥 BENEFICIARIOS</h3>
            <div class="estadistica-valor">2000</div>
            <div class="estadistica-desc">Alumnos beneficiados</div>
          </div>
          <div class="estadistica-card estadias">
            <h3>🎓 ESTADÍAS</h3>
            <div class="estadistica-valor">500</div>
            <div class="estadistica-desc">Estadías completadas</div>
          </div>
        </div>

        <!-- Estancia I -->
        <div class="tramite-section">
          <h3>📘 estancia1</h3>
          
          <div class="form-row">
            <div style="flex:1;">
              <h4>Documentos disponibles:</h4>
              <div class="upload-area">
                <p>📁 Subir documento</p>
                <p><strong>Configuraciones</strong></p>
                <p>No se vea... a verlos?</p>
                <button class="upload-btn">📎 Elegir Documento</button>
              </div>
            </div>
            
            <div style="flex:1;">
              <h4>Documentos enviados por alumno:</h4>
              <table class="documentos-table">
                <thead>
                  <tr>
                    <th>Alumno</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Juan Pérez</td>
                    <td>Carta de Aceptación.docx</td>
                    <td><span class="badge badge-pendiente">Pendiente</span></td>
                    <td>15/05/2024</td>
                  </tr>
                  <tr>
                    <td>María García</td>
                    <td>Informe Final.pdf</td>
                    <td><span class="badge badge-aprobado">Aprobado</span></td>
                    <td>10/05/2024</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Estancia II -->
        <div class="tramite-section">
          <h3>📗 estancia2</h3>
          
          <div class="form-row">
            <div style="flex:1;">
              <h4>Documentos disponibles:</h4>
              <div class="upload-area">
                <p>📁 Subir documento</p>
                <p><strong>Configuraciones</strong></p>
                <p>No se vea... a verlos?</p>
                <button class="upload-btn">📎 Elegir Documento</button>
              </div>
            </div>
            
            <div style="flex:1;">
              <h4>Documentos enviados por alumno:</h4>
              <table class="documentos-table">
                <thead>
                  <tr>
                    <th>Alumno</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Carlos López</td>
                    <td>Checklist.pdf</td>
                    <td><span class="badge badge-rechazado">Rechazado</span></td>
                    <td>08/05/2024</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Estadía -->
        <div class="tramite-section">
          <h3>🎓 estadía</h3>
          
          <div class="form-row">
            <div style="flex:1;">
              <h4>Documentos disponibles:</h4>
              <div class="upload-area">
                <p>📁 Subir documento</p>
                <p><strong>Configuraciones</strong></p>
                <p>No se vea... a verlos?</p>
                <button class="upload-btn">📎 Elegir Documento</button>
              </div>
            </div>
            
            <div style="flex:1;">
              <h4>Documentos enviados por alumno:</h4>
              <table class="documentos-table">
                <thead>
                  <tr>
                    <th>Alumno</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Ana Rodríguez</td>
                    <td>Formato Asesorías.docx</td>
                    <td><span class="badge badge-aprobado">Aprobado</span></td>
                    <td>12/05/2024</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <?php elseif ($seccion === 'estancia1'): ?>
        <!-- VISTA ALUMNO - ESTRICTA 1 -->
        <h2>📘 Estancia I</h2>
        
        <?php if (isset($_GET['accion']) && $_GET['accion'] === 'generar_solicitud'): ?>
          <!-- Formulario para generar solicitud -->
          <section class="form-section">
            <h3>📄 Generar solicitud (.DOCX)</h3>
            
            <form method="POST" action="procesar_solicitud.php">
              <div class="form-group">
                <label>Solicitud de</label>
                <div class="radio-group">
                  <label><input type="radio" name="tipo_solicitud" value="estancia1" checked> Estancia I</label>
                  <label><input type="radio" name="tipo_solicitud" value="estancia2"> Estancia II</label>
                  <label><input type="radio" name="tipo_solicitud" value="estadia"> Estadía</label>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="periodo">Período de Estancia/Estadía</label>
                  <input type="text" id="periodo" name="periodo" placeholder="Mayo-Agosto 2025" required>
                </div>
                <div class="checkbox-group">
                  <input type="checkbox" id="recurse" name="recurse">
                  <label for="recurse">¿Recurse?</label>
                </div>
              </div>
              
              <div class="divider"></div>
              
              <h4 class="subtitle">Datos del alumno</h4>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="nombre_completo">Nombre completo</label>
                  <input type="text" id="nombre_completo" name="nombre_completo" required>
                </div>
                <div class="form-group">
                  <label>Sexo</label>
                  <div class="radio-group">
                    <label><input type="radio" name="sexo" value="F"> F</label>
                    <label><input type="radio" name="sexo" value="M"> M</label>
                    <label><input type="radio" name="sexo" value="otro"> Otro</label>
                  </div>
                </div>
                <div class="form-group">
                  <label for="cuatrimestre">Cuatrimestre terminado</label>
                  <input type="number" id="cuatrimestre" name="cuatrimestre" min="1" max="12" required>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="matricula">Matrícula</label>
                  <input type="text" id="matricula" name="matricula" required>
                </div>
                <div class="form-group">
                  <label for="carrera">Carrera</label>
                  <input type="text" id="carrera" name="carrera" required>
                </div>
                <div class="form-group">
                  <label for="telefono">Teléfono</label>
                  <input type="tel" id="telefono" name="telefono" required>
                </div>
              </div>
              
              <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required>
              </div>
              
              <div class="divider"></div>
              
              <h4 class="subtitle">Datos para carta de presentación</h4>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="organizacion">Nombre de la organización</label>
                  <input type="text" id="organizacion" name="organizacion" required>
                </div>
                <div class="form-group">
                  <label for="destinatario">Cargo y nombre del destinatario</label>
                  <input type="text" id="destinatario" name="destinatario" placeholder="Ing. Juan Pérez - Director" required>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="telefono_organizacion">Teléfono de la organización</label>
                  <input type="tel" id="telefono_organizacion" name="telefono_organizacion" required>
                </div>
                <div class="form-group">
                  <label for="correo_destinatario">Correo del destinatario</label>
                  <input type="email" id="correo_destinatario" name="correo_destinatario" required>
                </div>
                <div class="form-group">
                  <label for="asesor">Nombre del asesor académico</label>
                  <input type="text" id="asesor" name="asesor" required>
                </div>
              </div>
              
              <div class="form-group">
                <label for="observaciones">Observaciones (Opcional)</label>
                <textarea id="observaciones" name="observaciones" rows="3"></textarea>
              </div>
              
              <div class="form-actions">
                <a href="?seccion=estancia1" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-generate">Generar y guardar</button>
              </div>
            </form>
          </section>
          
          <a class="back-btn" href="?seccion=estancia1">← Volver a Estancia I</a>
          
        <?php else: ?>
          <!-- Vista principal de Estancia I -->
          <a href="?seccion=estancia1&accion=generar_solicitud" class="login-btn" style="background:#3a0ca3; margin-bottom:15px;">📄 Generar solicitud</a>
          <p>Aquí podrás consultar los documentos disponibles para este trámite.</p>

          <section class="docs-section">
            <h3>📂 Documentos disponibles</h3>
            <ul class="doc-list"> 
              <li><a href="<?= $docDir ?>Carta de Aceptación.docx" download>📄 Carta de Aceptación.docx</a></li>
              <li><a href="<?= $docDir ?>Checklist.pdf" download>🧾 Checklist.pdf</a></li>
              <li><a href="<?= $docDir ?>Informe Final.docx" download>📘 Informe Final.docx</a></li>
              <li><a href="<?= $docDir ?>Formato de Asesorías.docx" download>🗂️ Formato de Asesorías.docx</a></li>
              <li><a href="<?= $docDir ?>Formato de Asesor Laboral.docx" download>🗂️ Formato de Asesor Laboral.docx</a></li>
              <li><a href="<?= $docDir ?>Encuesta de Satisfacción.pdf" download>✅ Encuesta de Satisfacción.pdf</a></li>
            </ul>
          </section>

          <a class="back-btn" href="?">← Escoger otro trámite</a>
        <?php endif; ?>
        
      <?php else: ?>
        <!-- VISTA PRINCIPAL ALUMNO -->
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']) ?> 👋</h2>
        <p>Selecciona el trámite que deseas realizar:</p>
        <div class="bienvenida">
          <a href="?seccion=estancia1" class="tarjeta"><div>📁</div><h3>Estancia I</h3></a>
          <a href="?seccion=estancia2" class="tarjeta"><div>📁</div><h3>Estancia II</h3></a>
          <a href="?seccion=estadia" class="tarjeta"><div>📁</div><h3>Estadía</h3></a>
          <a href="?seccion=delfin" class="tarjeta"><div>📁</div><h3>Delfín</h3></a>
          <a href="?seccion=conocete" class="tarjeta"><div>📁</div><h3>Conócete</h3></a>
        </div>
      <?php endif; ?>
    </main>
  </div>
<?php endif; ?>

<script>
function toggleMenu() {
  let menu = document.getElementById("menuOpciones");
  menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

// Función para simular subida de archivos
function simularSubida() {
  alert('Funcionalidad de subir archivos en desarrollo');
}
</script>
</body>
</html>