<?php
require_once __DIR__.'/../session_check.php';
require_once __DIR__.'/../db.php';
header('Content-Type: application/json; charset=utf-8');

$body = json_input();

$id = intval($body['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['error'=>'id manquant']); exit; }

// Read existing row for defaults
$cur = $pdo->prepare("SELECT * FROM reservations WHERE id=?");
$cur->execute([$id]);
$row = $cur->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); echo json_encode(['error'=>'Réservation introuvable']); exit; }

$room        = $body['room']       ?? $row['room'];
$date_start  = $body['date_start'] ?? $row['date_start'];
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start)) { http_response_code(400); echo json_encode(['error'=>'Date de début invalide']); exit; }
$nights      = isset($body['nights']) ? max(1, intval($body['nights'])) : intval($row['nights']);

$name        = $body['name'] ?? $row['name'];
$phone       = $body['phone'] ?? $row['phone'];
$count       = isset($body['count']) ? intval($body['count']) : intval($row['count']);
$occupancy   = $body['occupancy'] ?? $row['occupancy'];

$breakfast_count = isset($body['breakfast_count']) ? max(0,intval($body['breakfast_count'])) : intval($row['breakfast_count']);
$halfboard_count = isset($body['halfboard_count']) ? max(0,intval($body['halfboard_count'])) : intval($row['halfboard_count']);
$fullboard_count = isset($body['fullboard_count']) ? max(0,intval($body['fullboard_count'])) : intval($row['fullboard_count']);

// Flags derived if counters > 0
$breakfast   = ($breakfast_count > 0) ? 'oui' : (($body['breakfast'] ?? $row['breakfast']) === 'oui' ? 'oui' : 'non');
$halfboard   = ($halfboard_count > 0) ? 'oui' : (($body['halfboard'] ?? $row['halfboard']) === 'oui' ? 'oui' : 'non');
$fullboard   = ($fullboard_count > 0) ? 'oui' : (($body['fullboard'] ?? $row['fullboard']) === 'oui' ? 'oui' : 'non');

$tr_arr      = (($body['transfer_arrivee'] ?? $row['transfer_arrivee']) === 'oui') ? 'oui' : 'non';
$tr_dep      = (($body['transfer_depart']  ?? $row['transfer_depart'])  === 'oui') ? 'oui' : 'non';
$flight      = $body['flight'] ?? $row['flight'];
$invoice     = $body['invoice'] ?? $row['invoice'];
$notes       = $body['notes'] ?? $row['notes'];

// Blocage / chambre demandée
$chambre_demande = isset($body['chambre_demande']) ? (intval($body['chambre_demande']) ? 1 : 0) : intval($row['chambre_demande'] ?? 0);
$status = $body['status'] ?? ($row['status'] ?? 'reservation');
if (!in_array($status, ['reservation','hold','maintenance'], true)) {
  $status = ($row['status'] ?? 'reservation');
}
$block_reason = isset($body['block_reason']) ? trim($body['block_reason']) : ($row['block_reason'] ?? '');

// Auto : checkbox chambre demandée => hold (si rien d'autre)
if ($status === 'reservation' && $chambre_demande === 1) {
  $status = 'hold';
  if ($block_reason === '') $block_reason = 'Option client (chambre demandée)';
}

// Un blocage ne doit pas conserver des données de réservation
if ($status !== 'reservation') {
  $count = 0;
  $occupancy = '';
  $breakfast_count = 0;
  $halfboard_count = 0;
  $fullboard_count = 0;
  $breakfast = 'non';
  $halfboard = 'non';
  $fullboard = 'non';
  $tr_arr = 'non';
  $tr_dep = 'non';
  $flight = '';
}

// Overlap guard excluding current id
$new_end = (new DateTime($date_start))->modify("+{$nights} day")->format('Y-m-d');
$chk = $pdo->prepare("SELECT COUNT(*) c FROM reservations
  WHERE room=? AND id<>? AND NOT (DATE_ADD(date_start, INTERVAL nights DAY) <= ? OR ? <= date_start)");
$chk->execute([$room, $id, $date_start, $new_end]);
if (intval($chk->fetchColumn()) > 0) {
  http_response_code(409);
  echo json_encode(['error'=>'Chevauchement détecté pour cette chambre.']);
  exit;
}

$sql = "UPDATE reservations SET room=?, date_start=?, nights=?, name=?, phone=?, count=?, occupancy=?,
  breakfast=?, halfboard=?, fullboard=?, breakfast_count=?, halfboard_count=?, fullboard_count=?,
  transfer_arrivee=?, transfer_depart=?, flight=?, invoice=?, notes=?,
  chambre_demande=?, status=?, block_reason=?
  WHERE id=?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$room, $date_start, $nights, $name, $phone, $count, $occupancy,
                $breakfast, $halfboard, $fullboard, $breakfast_count, $halfboard_count, $fullboard_count,
                $tr_arr, $tr_dep, $flight, $invoice, $notes,
                $chambre_demande, $status, $block_reason,
                $id]);

echo json_encode(['ok'=>true,'id'=>$id]);

