<?php
require_once __DIR__.'/../db.php';
header('Content-Type: application/json; charset=utf-8');

// month=YYYY-MM (default: current month)
$month = $_GET['month'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
  http_response_code(400);
  echo json_encode(['error' => 'Paramètre month invalide.']);
  exit;
}

$start = $month . '-01';
$start_dt = new DateTime($start);
$end_dt = (clone $start_dt)->modify('first day of next month');
$end = $end_dt->format('Y-m-d');

// We return ranges that make the room unavailable, WITHOUT any client details.
$sql = "SELECT id, room, date_start, nights
        FROM reservations
        WHERE date_start < :end
          AND DATE_ADD(date_start, INTERVAL nights DAY) > :start";

$stmt = $pdo->prepare($sql);
$stmt->execute([':start' => $start, ':end' => $end]);

$out = [];
while ($r = $stmt->fetch()) {
  $date_start = $r['date_start'];
  $nights = intval($r['nights']);
  if ($nights < 1) $nights = 1;

  $ds = new DateTime($date_start);
  $de = (clone $ds)->modify('+' . $nights . ' day'); // exclusive end

  $out[] = [
    'room' => $r['room'],
    'date_start' => $ds->format('Y-m-d'),
    'date_end' => $de->format('Y-m-d'),
  ];
}

echo json_encode($out);
