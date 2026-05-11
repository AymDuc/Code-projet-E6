<?php require_once __DIR__.'/inc_public.php'; public_header('Tarifs', 'tarifs'); ?>
<main>
  <section class="page-hero">
    <div class="wrap narrow">
      <span class="eyebrow">Tarifs</span>
      <h1>Tarifs indicatifs de l’Hôtel Hinano</h1>
      <p class="lead">Prix affichés en FCP. Des conditions particulières peuvent s’appliquer pour les professionnels ou les séjours spécifiques.</p>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <div class="tarifs-grid">
        <div class="tarif-row"><span>Chambre patio</span><strong>8 000 FCP</strong></div>
        <div class="tarif-row"><span>Chambre lagon / montagne</span><strong>9 000 FCP</strong></div>
        <div class="tarif-row"><span>Lit supplémentaire</span><strong>2 000 FCP</strong></div>
        <div class="tarif-row"><span>Enfant (-12 ans)</span><strong>1 000 FCP</strong></div>
        <div class="tarif-row"><span>Bébé (-2 ans)</span><strong>Gratuit</strong></div>
        <div class="tarif-row"><span>Petit-déjeuner</span><strong>1 000 FCP</strong></div>
        <div class="tarif-row"><span>Taxe de séjour</span><strong>60 FCP / personne / nuit</strong></div>
      </div>

      <div class="note-box">
        <strong>Moyens de paiement :</strong> chèque • virement • numéraire (pas de carte bancaire).<br>
        <strong>Annulation :</strong> moins de 24h avant check-in = nuit facturée.
      </div>

      <div class="note-box page-nav-box">
        <strong>Ensuite :</strong>
        <a href="services.php">voir les services</a> •
        <a href="disponibilites.php">consulter le planning</a> •
        <a href="contact.php">faire une demande</a>
      </div>
    </div>
  </section>
</main>
<?php public_footer(false); ?>
