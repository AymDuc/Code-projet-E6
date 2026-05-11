<?php
require_once __DIR__.'/../session_check.php';
require_once __DIR__.'/../db.php';
header('Content-Type: application/json; charset=utf-8');

$body = json_input();

$room        = $body['room'] ?? 'Chambre 1';
$date_start  = $body['date_start'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start)) { http_response_code(400); echo json_encode(['error'=>'Date de début invalide']); exit; }
$nights      = max(1, intval($body['nights'] ?? 1));
$name        = $body['name'] ?? '';
$phone       = $body['phone'] ?? '';
$count       = intval($body['count'] ?? 0);
$occupancy   = $body['occupancy'] ?? '';

// New counters (take precedence)
$breakfast_count = max(0, intval($body['breakfast_count'] ?? 0));
$halfboard_count = max(0, intval($body['halfboard_count'] ?? 0));
$fullboard_count = max(0, intval($body['fullboard_count'] ?? 0));

// Legacy flags -> derive from counters if any > 0
$breakfast   = ($breakfast_count > 0) ? 'oui' : ((($body['breakfast'] ?? 'non') === 'oui') ? 'oui' : 'non');
$halfboard   = ($halfboard_count > 0) ? 'oui' : ((($body['halfboard'] ?? 'non') === 'oui') ? 'oui' : 'non');
$fullboard   = ($fullboard_count > 0) ? 'oui' : ((($body['fullboard'] ?? 'non') === 'oui') ? 'oui' : 'non');

$tr_arr      = (($body['transfer_arrivee'] ?? 'non') === 'oui') ? 'oui' : 'non';
$tr_dep      = (($body['transfer_depart']  ?? 'non') === 'oui') ? 'oui' : 'non';
$flight      = $body['flight'] ?? '';
$invoice     = $body['invoice'] ?? '';
$notes       = $body['notes'] ?? '';

// Blocage / chambre demandée
$chambre_demande = intval($body['chambre_demande'] ?? 0) ? 1 : 0;
$status = $body['status'] ?? 'reservation';
if (!in_array($status, ['reservation','hold','maintenance'], true)) {
  $status = 'reservation';
}
$block_reason = trim($body['block_reason'] ?? '');

// Convention : si checkbox "chambre demandée" est cochée et qu'aucun status n'est donné,
// on bascule automatiquement en "hold" (blocage option client) pour empêcher une autre résa.
if ($status === 'reservation' && $chambre_demande === 1) {
  $status = 'hold';
  if ($block_reason === '') $block_reason = 'Option client (chambre demandée)';
}


// Un blocage ne doit pas transporter de données de réservation
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

// Overlap guard (same room)
$new_end = (new DateTime($date_start))->modify("+{$nights} day")->format('Y-m-d');
$chk = $pdo->prepare("SELECT COUNT(*) c FROM reservations
  WHERE room=? AND NOT (DATE_ADD(date_start, INTERVAL nights DAY) <= ? OR ? <= date_start)");
$chk->execute([$room, $date_start, $new_end]);
if (intval($chk->fetchColumn()) > 0) {
  http_response_code(409);
  echo json_encode(['error'=>'Chevauchement détecté pour cette chambre.']);
  exit;
}

$sql = "INSERT INTO reservations
(room, date_start, nights, name, phone, count, occupancy,
 breakfast, halfboard, fullboard,
 breakfast_count, halfboard_count, fullboard_count,
 transfer_arrivee, transfer_depart, flight, invoice, notes,
 chambre_demande, status, block_reason)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$room, $date_start, $nights, $name, $phone, $count, $occupancy,
                $breakfast, $halfboard, $fullboard,
                $breakfast_count, $halfboard_count, $fullboard_count,
                $tr_arr, $tr_dep, $flight, $invoice, $notes,
                $chambre_demande, $status, $block_reason]);

echo json_encode(['ok'=>true, 'id'=>$pdo->lastInsertId()]);
