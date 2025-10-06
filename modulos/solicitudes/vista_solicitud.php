<?php
session_start();
$prev = $_SESSION['solicitud_preview'] ?? null;
if (!$prev) {
  header('Location: estancia1.php'); // fallback
  exit;
}

$vals    = $prev['vals'];
$seccion = $prev['seccion'];
$file    = $prev['file'];
$fecha   = $prev['fecha'];

function e($k){ global $vals; return htmlspecialchars($vals[$k] ?? '—', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Vista previa — Solicitud</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<style>
  :root{ --b:#1d2433; --m:#6b7280; --line:#d9dee8; --brand:#9b1b3f; }
  *{box-sizing:border-box}
  body{font-family:"Montserrat",Arial,sans-serif;margin:24px;color:var(--b);background:#fff}
  .actions{position:sticky;top:0;background:#fff;padding:10px 0;margin:-10px 0 12px;display:flex;gap:8px;justify-content:flex-end}
  .btn{border:none;border-radius:10px;padding:9px 14px;cursor:pointer}
  .btn.primary{background:linear-gradient(180deg,#7f1735,#9b1b3f);color:#fff}
  .btn.ghost{background:#eef2f6}
  .sheet{max-width:900px;margin:0 auto;border:1px solid var(--line);border-radius:12px;padding:28px 28px 36px}
  .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
  .code{font-size:.9rem;color:var(--m)}
  h1{font-size:1.3rem;margin:0 0 8px;color:var(--brand);font-weight:600}
  .hr{height:1px;background:var(--line);margin:14px 0 18px}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
  label{display:flex;flex-direction:column;font-size:.9rem;gap:6px}
  .val{border:1px solid var(--line);border-radius:8px;padding:10px 12px}
  .row{display:flex;gap:16px;align-items:center;flex-wrap:wrap}
  .pill{background:#f0f3f8;border:1px solid var(--line);border-radius:999px;padding:6px 10px;font-size:.85rem}
  .muted{color:var(--m);font-size:.9rem}
  .firm{margin-top:28px;display:flex;justify-content:center;gap:60px}
  .firm .line{height:1px;background:#cfd6e2;width:260px;margin-top:40px}
  @media print { .actions{display:none} .sheet{border:none} body{margin:0} }
</style>
</head>
<body>

<div class="actions">
  <a class="btn ghost" href="<?php echo htmlspecialchars($seccion . '.php'); ?>">Volver</a>
  <a class="btn" href="<?php echo 'entregados/' . htmlspecialchars($seccion) . '/' . htmlspecialchars($file); ?>" download>Descargar DOCX</a>
  <button class="btn primary" onclick="window.print()">Imprimir / Guardar PDF</button>
</div>

<div class="sheet">
  <div class="head">
    <div>
      <div class="code">UPA-F-EE-05/R01 · Revisión 04</div>
      <h1>SOLICITUD DE ESTANCIA Y/O ESTADÍA</h1>
      <div class="muted">Generada: <?php echo htmlspecialchars($fecha); ?></div>
    </div>
    <div class="pill"><?php echo htmlspecialchars($vals['solicitud_de'] ?: '—'); ?></div>
  </div>

  <div class="hr"></div>

  <div class="row">
    <div class="pill">Periodo: <?php echo e('periodo'); ?></div>
    <div class="pill">Recurse: <?php echo htmlspecialchars(empty($vals['recurse']) ? 'No' : $vals['recurse']); ?></div>
  </div>

  <h2 style="margin:18px 0 8px;font-size:1.05rem">Datos del alumno</h2>
  <div class="grid-3">
    <label>Nombre<span class="val"><?php echo e('alumno_nombre'); ?></span></label>
    <label>Sexo<span class="val"><?php echo e('alumno_sexo'); ?></span></label>
    <label>Cuatrimestre terminado<span class="val"><?php echo e('cuatrimestre'); ?></span></label>
  </div>
  <div class="grid-3" style="margin-top:12px">
    <label>Matrícula<span class="val"><?php echo e('matricula'); ?></span></label>
    <label>Carrera<span class="val"><?php echo e('carrera'); ?></span></label>
    <label>Teléfono<span class="val"><?php echo e('tel_alumno'); ?></span></label>
  </div>
  <div class="grid" style="margin-top:12px">
    <label>Correo electrónico<span class="val"><?php echo e('mail_alumno'); ?></span></label>
  </div>

  <h2 style="margin:18px 0 8px;font-size:1.05rem">Datos para elaborar la carta de presentación</h2>
  <div class="grid">
    <label>Nombre de la organización<span class="val"><?php echo e('org_nombre'); ?></span></label>
    <label>Cargo y nombre del destinatario<span class="val"><?php echo e('destinatario'); ?></span></label>
  </div>
  <div class="grid" style="margin-top:12px">
    <label>Teléfono de la organización<span class="val"><?php echo e('org_tel'); ?></span></label>
    <label>Correo del destinatario<span class="val"><?php echo e('dest_mail'); ?></span></label>
  </div>
  <div class="grid" style="margin-top:12px">
    <label>Nombre del asesor académico<span class="val"><?php echo e('asesor_academico'); ?></span></label>
  </div>

  <h2 style="margin:18px 0 8px;font-size:1.05rem">Observaciones</h2>
  <div class="val" style="min-height:64px"><?php echo nl2br(htmlspecialchars($vals['observaciones'] ?? '—', ENT_QUOTES, 'UTF-8')); ?></div>

  <div class="firm">
    <div>
      <div class="line"></div>
      <div style="text-align:center;margin-top:6px;">Nombre del Alumno y Firma</div>
    </div>
  </div>
</div>

</body>
</html>
