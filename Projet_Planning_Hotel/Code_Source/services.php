<?php require_once __DIR__.'/inc_public.php'; public_header('Services', 'services'); ?>
<main>
  <section class="page-hero">
    <div class="wrap narrow">
      <span class="eyebrow">Services</span>
      <h1>Des services utiles pour un séjour plus simple</h1>
      <p class="lead">Petit-déjeuner, navette, linge et petits plus pour rendre l’arrivée et le séjour plus fluides.</p>
    </div>
  </section>

  <section class="section alt">
    <div class="wrap">
      <div class="cards">
        <div class="card">
          <h3>Petit-déjeuner</h3>
          <p class="muted">Continental : boisson chaude, pain, beurre, confiture, viennoiserie, fromage, jus.</p>
          <div class="price">1 000 FCP</div>
        </div>
        <div class="card">
          <h3>Navette aéroport</h3>
          <p class="muted">Aller ou retour selon disponibilité. Sinon, taxis disponibles à l’aéroport.</p>
          <div class="price">1 000 FCP</div>
        </div>
        <div class="card">
          <h3>Lingerie</h3>
          <p class="muted">Lavage / séchage de 1 à 10 kg pour le linge personnel.</p>
          <div class="price">2 000 FCP</div>
        </div>
      </div>

      <div class="note-box page-nav-box">
        <strong>Besoin d’enchaîner ?</strong>
        <a href="chambres.php">retour aux chambres</a> •
        <a href="tarifs.php">voir les tarifs</a> •
        <a href="contact.php">nous contacter</a>
      </div>
    </div>
  </section>
</main>
<?php public_footer(false); ?>
