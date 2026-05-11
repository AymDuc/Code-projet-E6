<?php
require_once __DIR__.'/../session_check.php';
require_once __DIR__.'/../db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="reservations.csv"');

$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';
$room  = $_GET['room'] ?? '';

$sql = "SELECT id, room, date_start, nights, name, phone, count, occupancy,
               breakfast, halfboard, fullboard,
               breakfast_count, halfboard_count, fullboard_count,
               transfer_arrivee, transfer_depart, flight, invoice, notes,
               chambre_demande, status, block_reason
        FROM reservations WHERE 1=1";
$prm = [];
if ($start !== '') { $sql .= " AND date_start >= ?"; $prm[] = $start; }
if ($end   !== '') { $sql .= " AND date_start <= ?"; $prm[] = $end; }
if ($room  !== '') { $sql .= " AND room = ?";        $prm[] = $room; }
$sql .= " ORDER BY room, date_start, id";

$out = fopen('php://output', 'w');
fputcsv($out, ['id','room','date_start','nights','name','phone','count','occupancy',
               'breakfast','halfboard','fullboard',
               'breakfast_count','halfboard_count','fullboard_count',
               'transfer_arrivee','transfer_depart','flight','invoice','notes',
               'chambre_demande','status','block_reason']);

$stmt = $pdo->prepare($sql);
$stmt->execute($prm);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  fputcsv($out, $row);
}
fclose($out);
