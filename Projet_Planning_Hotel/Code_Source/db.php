<?php
$DB_HOST = 'localhost';
$DB_NAME = 'hotel';
$DB_USER = 'root';
$DB_PASS = '';  // A modifier selon l'environnement local/serveur

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'error'   => 'Erreur de connexion à la base'
      ]);
  exit;
}

function json_input() {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}
