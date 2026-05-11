<?php require_once __DIR__.'/session_check.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Planning Hôtel </title>
  <link rel="stylesheet" href="style_glow.css" />
</head>
<body>
<header class="topbar">
  <img src="logo.png" alt="Logo" class="logo">
  <h1>Planning Hôtel</h1>
  <div class="controls">
    <button id="exportCsv">Exporter CSV</button>
    <select id="viewMode">
      <option value="month">Mois</option>
      <option value="week">Semaine</option>
      <option value="year">Année</option>
      <option value="gantt">Gantt (par chambre)</option>
    </select>
    <button id="prevBtn">◀</button>
    <div id="periodLabel"></div>
    <button id="nextBtn">▶</button>
  </div>
</header>

<main class="container onecol">
  <section class="planner" id="plannerSection">
    <table id="plannerTable">
      <thead><tr id="plannerHeader"></tr></thead>
      <tbody id="plannerBody"></tbody>
    </table>
    <div id="ganttContainer" class="gantt" hidden></div>
  </section>
</main>

<!-- Modal -->
<div class="modal" id="resModal" hidden>
  <div class="modal-content">
    <div class="modal-header">
      <div>
        <div class="modal-kicker">Fiche chambre</div>
        <h3 id="modalTitle">Réservation</h3>
      </div>
      <button type="button" id="modalClose" class="icon-btn" aria-label="Fermer">✕</button>
    </div>

    <form id="resForm">
      <input type="hidden" name="id" id="resId">

      <div class="form-grid top-grid">
        <label>
          <span class="field-label">Type</span>
          <select name="status" id="resStatus">
            <option value="reservation">Réservation confirmée</option>
            <option value="hold">Blocage / option client</option>
            <option value="maintenance">Blocage maintenance / travaux</option>
          </select>
        </label>

        <label>
          <span class="field-label">Chambre</span>
          <select required name="room" id="resRoom">
            <option value="Chambre 1">Chambre 1</option>
            <option value="Chambre 2">Chambre 2</option>
            <option value="Chambre 3">Chambre 3</option>
            <option value="Chambre 4">Chambre 4</option>
            <option value="Chambre 5">Chambre 5</option>
            <option value="Chambre 6">Chambre 6</option>
            <option value="Chambre 7">Chambre 7</option>
            <option value="Chambre 8">Chambre 8</option>
            <option value="Chambre 9">Chambre 9</option>
            <option value="Chambre 10">Chambre 10</option>
          </select>
        </label>

        <label>
          <span class="field-label">Arrivée</span>
          <input required type="date" name="date_start" id="resDateStart" />
        </label>

        <label>
          <span class="field-label">Nuits</span>
          <input required type="number" min="1" name="nights" id="resNights" value="1" />
        </label>
      </div>

      <div class="date-summary" id="dateSummary">
        <div class="summary-card">
          <span class="summary-label">Séjour</span>
          <strong id="resStaySummary">—</strong>
        </div>
        <div class="summary-card">
          <span class="summary-label">Départ</span>
          <strong id="resDateEndPreview">—</strong>
        </div>
      </div>

      <section class="form-section client-section">
        <div class="section-title-row">
          <h4>Infos client</h4>
          <label class="checkbox-chip">
            <input type="checkbox" id="resChambreDemande" />
            <span>Chambre demandée</span>
          </label>
        </div>

        <div class="form-grid two-cols reservation-only">
          <label>
            <span class="field-label">Nom client</span>
            <input name="name" id="resName" placeholder="Nom Prénom" />
          </label>
          <label>
            <span class="field-label">Téléphone</span>
            <input name="phone" id="resPhone" placeholder="+689 ..." />
          </label>
          <label>
            <span class="field-label">Nombre de personnes</span>
            <input type="number" min="0" name="count" id="resCount" value="0" />
          </label>
          <label>
            <span class="field-label">Détail couchages</span>
            <input name="occupancy" id="resOccupancy" placeholder="Ex : 2+2" />
          </label>
        </div>

        <div class="form-grid block-only" id="blockReasonWrap">
          <label>
            <span class="field-label">Raison du blocage</span>
            <input name="block_reason" id="resBlockReason" placeholder="Ex : maintenance clim / option client" />
          </label>
        </div>
      </section>

      <section class="form-section reservation-only" id="mealSection">
        <h4>Repas & services</h4>
        <div class="form-grid three-cols compact-fields">
          <label>
            <span class="field-label">Petits-déjeuners</span>
            <input type="number" min="0" name="breakfast_count" id="resBreakfastCount" value="0" />
          </label>
          <label>
            <span class="field-label">Demi-pensions</span>
            <input type="number" min="0" name="halfboard_count" id="resHalfboardCount" value="0" />
          </label>
          <label>
            <span class="field-label">Pensions complètes</span>
            <input type="number" min="0" name="fullboard_count" id="resFullboardCount" value="0" />
          </label>
        </div>
      </section>

      <section class="form-section transfer-fields reservation-only" id="transferSection">
        <h4>Transferts</h4>
        <div class="form-grid three-cols compact-fields">
          <label>
            <span class="field-label">Transfert arrivée</span>
            <select name="transfer_arrivee" id="resTransferArrivee">
              <option value="non">non</option>
              <option value="oui">oui</option>
            </select>
          </label>
          <label>
            <span class="field-label">Transfert départ</span>
            <select name="transfer_depart" id="resTransferDepart">
              <option value="non">non</option>
              <option value="oui">oui</option>
            </select>
          </label>
          <label>
            <span class="field-label">Vol</span>
            <input name="flight" id="resFlight" placeholder="VTxxx" />
          </label>
        </div>
      </section>

      <section class="form-section">
        <h4>Suivi</h4>
        <div class="form-grid two-cols">
          <label class="reservation-only">
            <span class="field-label">Facture</span>
            <input name="invoice" id="resInvoice" placeholder="FAC-0001" />
          </label>
          <label class="full-width">
            <span class="field-label">Notes</span>
            <textarea name="notes" id="resNotes" rows="4" placeholder="Notes internes, demandes client, remarques ménage..."></textarea>
          </label>
        </div>
      </section>

      <div class="actions">
        <button type="button" id="deleteBtn" class="danger" hidden>Supprimer</button>
        <div class="actions-right">
          <button type="button" id="cancelBtn">Annuler</button>
          <button type="submit" id="saveBtn" class="primary">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="script.js"></script>
</body>
</html>
