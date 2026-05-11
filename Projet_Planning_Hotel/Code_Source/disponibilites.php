<?php require_once __DIR__.'/inc_public.php'; public_header('Planning des disponibilités', 'planning'); ?>
<main>
  <section class="page-hero">
    <div class="wrap narrow">
      <span class="eyebrow">Planning</span>
      <h1>Disponibilités en lecture seule</h1>
      <p class="lead">Affichage simplifié pour les clients : uniquement disponible / occupé, sans nom ni détail interne.</p>
    </div>
  </section>

  <section class="section alt">
    <div class="wrap">
      <div class="planning-head">
        <div>
          <h2>Planning public</h2>
          <p class="lead">Pour une réservation, contacte directement l’hôtel avec tes dates et le nombre de personnes.</p>
        </div>
        <div class="planning-controls">
          <button class="btn-secondary" id="pubPrev">◀</button>
          <div class="period" id="pubLabel"></div>
          <button class="btn-secondary" id="pubNext">▶</button>
        </div>
      </div>

      <div class="planning-wrap">
        <div class="legend">
          <span class="chip ok">Libre</span>
          <span class="chip no">Occupé</span>
        </div>
        <div class="planning-table-wrap">
          <table class="pub-planning" id="pubTable" aria-label="Planning des disponibilités"></table>
        </div>
        <div class="muted small">
          Besoin d’une confirmation ? Appelle ou écris à l’hôtel pour valider la disponibilité exacte.
        </div>
      </div>
    </div>
  </section>
</main>
<?php public_footer(true); ?>
