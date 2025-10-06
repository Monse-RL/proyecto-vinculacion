<?php
// Guarda una solicitud en la BD después de generar el PDF
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db.php';

// Simulación de usuario autenticado (luego lo conectamos a login)
$ID_USUARIO = 1;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false, 'msg'=>'Método no permitido']);
  exit;
}

// Campos esperados
$tipo        = $_POST['tipo']        ?? '';
$archivo_pdf = $_POST['archivo_pdf'] ?? '';
$estado      = $_POST['estado']      ?? 'pendiente';

// Validaciones mínimas
$tiposValidos = ['estancia1','estancia2','estadia'];
if (!in_array($tipo, $tiposValidos, true)) {
  echo json_encode(['ok'=>false, 'msg'=>'Tipo de solicitud inválido']);
  exit;
}
if ($archivo_pdf === '' || !preg_match('/^[A-Za-z0-9._\-]+\.pdf$/', $archivo_pdf)) {
  echo json_encode(['ok'=>false, 'msg'=>'Nombre de PDF inválido']);
  exit;
}

try {
  $sql = "INSERT INTO solicitudes (id_usuario, tipo, estado, archivo_pdf) VALUES (?,?,?,?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$ID_USUARIO, $tipo, $estado, $archivo_pdf]);

  echo json_encode(['ok'=>true, 'msg'=>'Solicitud guardada', 'id'=>$pdo->lastInsertId()]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false, 'msg'=>'Error al guardar', 'error'=>$e->getMessage()]);
}
