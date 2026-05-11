<?php
function public_header(string $title, string $active = ''): void {
  $fullTitle = $title === 'Accueil' ? 'Hôtel Hinano — Raiatea' : $title . ' — Hôtel Hinano';
  ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($fullTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="Hôtel familial au centre d'Uturoa (Raiatea), à 3 minutes de l'aéroport. 10 chambres climatisées, wifi gratuit.">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="icon" type="image/png" sizes="64x64" href="favicon-64.png">
  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style_site.css">
</head>
<body>
  <header class="site-header">
    <div class="wrap header-inner">
      <a class="brand" href="index.php" aria-label="Hôtel Hinano">
        <img src="favicon.png" alt="Symbole Hôtel Hinano" class="brand-emblem">
        <div class="brand-text">
          <img src="logo-wordmark.png" alt="Hôtel Hinano" class="brand-wordmark">
          <div class="brand-sub">Le seul hôtel en centre ville • Raiatea</div>
        </div>
      </a>

      <nav class="nav" aria-label="Navigation">
        <a href="index.php" class="<?= $active === 'accueil' ? 'active' : '' ?>">Accueil</a>
        <a href="chambres.php" class="<?= $active === 'chambres' ? 'active' : '' ?>">Chambres</a>
        <a href="services.php" class="<?= $active === 'services' ? 'active' : '' ?>">Services</a>
        <a href="tarifs.php" class="<?= $active === 'tarifs' ? 'active' : '' ?>">Tarifs</a>
        <a href="disponibilites.php" class="<?= $active === 'planning' ? 'active' : '' ?>">Planning</a>
        <a href="contact.php" class="cta <?= $active === 'contact' ? 'active' : '' ?>">Contact</a>
        <a href="login.php" class="ghost" title="Espace gérance / employés">Espace pro</a>
      </nav>

      <button class="burger" id="burger" aria-label="Ouvrir le menu">
        <span></span><span></span><span></span>
      </button>
    </div>

    <div class="mobile-nav" id="mobileNav" hidden>
      <a href="index.php">Accueil</a>
      <a href="chambres.php">Chambres</a>
      <a href="services.php">Services</a>
      <a href="tarifs.php">Tarifs</a>
      <a href="disponibilites.php">Planning</a>
      <a href="contact.php">Contact</a>
      <a href="login.php">Espace pro</a>
    </div>
  </header>
  <?php
}

function public_footer(bool $loadPlanning = false): void {
  ?>
  <footer class="footer">
    <div class="wrap footer-inner">
      <div>© <?= date('Y') ?> Hôtel Hinano • Raiatea</div>
      <div class="muted">Site vitrine Hôtel Hinano • pages harmonisées avec le logo officiel.</div>
    </div>
  </footer>

  <?php if ($loadPlanning): ?>
  <script src="public_planning.js"></script>
  <?php endif; ?>
  <script>
    const burger = document.getElementById('burger');
    const mobileNav = document.getElementById('mobileNav');
    burger?.addEventListener('click', () => {
      const hidden = mobileNav.hasAttribute('hidden');
      if (hidden) mobileNav.removeAttribute('hidden'); else mobileNav.setAttribute('hidden','');
    });
    mobileNav?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => mobileNav.setAttribute('hidden','')));
  </script>
</body>
</html>
  <?php
}
