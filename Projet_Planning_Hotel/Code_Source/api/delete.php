<?php
require_once __DIR__.'/../session_check.php';
require_once __DIR__.'/../db.php';
header('Content-Type: application/json; charset=utf-8');
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['error'=>'id manquant']); exit; }
$stmt = $pdo->prepare("DELETE FROM reservations WHERE id=?");
$stmt->execute([$id]);
echo json_encode(['ok'=>true]);
