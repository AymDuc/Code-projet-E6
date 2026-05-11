<?php
require_once __DIR__.'/../session_check.php';
require_once __DIR__.'/../db.php';
header('Content-Type: application/json; charset=utf-8');

$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';
$room  = $_GET['room'] ?? '';

$sql = "SELECT id, room, date_start, nights, name, phone, `count`, occupancy,
               breakfast, halfboard, fullboard,
               breakfast_count, halfboard_count, fullboard_count,
               transfer_arrivee, transfer_depart, flight, invoice, notes,
               chambre_demande, status, block_reason
        FROM reservations WHERE 1=1";
$prm = [];
if ($start !== '' && $end !== '') {
  $sql .= " AND date_start < ? AND DATE_ADD(date_start, INTERVAL nights DAY) > ?";
  $prm[] = $end;
  $prm[] = $start;
} elseif ($start !== '') {
  $sql .= " AND DATE_ADD(date_start, INTERVAL nights DAY) > ?";
  $prm[] = $start;
} elseif ($end !== '') {
  $sql .= " AND date_start < ?";
  $prm[] = $end;
}
if ($room !== '') { $sql .= " AND room = ?"; $prm[] = $room; }
$sql .= " ORDER BY room, date_start, id";

$stmt = $pdo->prepare($sql);
$stmt->execute($prm);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['items'=>$items], JSON_UNESCAPED_UNICODE);
