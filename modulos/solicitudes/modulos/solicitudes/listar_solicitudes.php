<?php
require_once __DIR__ . '/../../includes/db.php';

// Trae todas las solicitudes (luego filtramos por usuario/rol)
$sql = "SELECT s.id, s.tipo, s.fecha, s.estado, s.archivo_pdf, u.nombre AS alumno
        FROM solicitudes s
        LEFT JOIN usuarios u ON u.id = s.id_usuario
        ORDER BY s.id DESC";
$rows = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitudes — Vinculación</title>
  <link rel="stylesheet" href="../../assets/css/styles.css?v=14">
  <style>
    .tabla { width:100%; border-collapse:collapse; background:#fff; }
    .tabla th, .tabla td { border:1px solid #e5e7eb; padding:10px; font-size:.95rem; }
    .tabla th { background:#f8f9fb; text-align:left; }
    .pill{padding:4px 8px;border-radius:999px;font-size:.8rem;}
    .p-pend{background:#fff7ed;border:1px solid #fed7aa}
    .p-apr{background:#ecfdf5;border:1px solid #a7f3d0}
    .p-rec{background:#fef2f2;border:1px solid #fecaca}
    .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
    h1{margin-bottom:14px}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>📄 Solicitudes registradas</h1>

    <?php if (!$rows): ?>
      <p>No hay solicitudes aún.</p>
    <?php else: ?>
      <table class="tabla">
        <thead>
          <tr>
            <th>ID</th>
            <th>Alumno</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Archivo PDF</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $r): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td><?= htmlspecialchars($r['alumno'] ?? '—') ?></td>
              <td><?= htmlspecialchars(ucfirst($r['tipo'])) ?></td>
              <td><?= htmlspecialchars($r['fecha']) ?></td>
              <td>
                <?php
                  $map = ['pendiente'=>'p-pend','aprobada'=>'p-apr','rechazada'=>'p-rec'];
                  $cls = $map[$r['estado']] ?? 'p-pend';
                ?>
                <span class="pill <?= $cls ?>"><?= htmlspecialchars($r['estado']) ?></span>
              </td>
              <td>
                <?php if (!empty($r['archivo_pdf'])): ?>
                  <a class="descargar" href="/entregados/solicitudes_pdf/<?= rawurlencode($r['archivo_pdf']) ?>" target="_blank">Ver PDF</a>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
