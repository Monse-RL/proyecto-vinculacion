<?php
/**
 * Conexión PDO para MySQL (XAMPP/hosting).
 * En XAMPP usualmente usuario = root y contraseña = "".
 * En hosting cambia $user y $pass por los de tu panel.
 */
$host = "localhost";
$dbname = "vinculacion";
$user = "root";
$pass = "";

// Crea $pdo global
try {
  $pdo = new PDO(
    "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  die("Error de conexión a la BD: " . htmlspecialchars($e->getMessage()));
}
