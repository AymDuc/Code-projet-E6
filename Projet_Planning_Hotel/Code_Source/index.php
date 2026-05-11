<?php require_once __DIR__.'/inc_public.php'; public_header('Accueil', 'accueil'); ?>
  <main>
    <section class="hero">
      <div class="wrap hero-grid">
        <div class="hero-text">
          <h1>Bienvenue à l’Hôtel Hinano</h1>
          <p>
            Un petit hôtel familial au cœur d’Uturoa, pratique pour les séjours pro, les escales,
            les visites de Raiatea et les départs vers Taha’a.
          </p>
          <div class="hero-badges">
            <span class="badge">À ~3 min de l’aéroport</span>
            <span class="badge">10 chambres</span>
            <span class="badge">Wifi gratuit</span>
            <span class="badge">Climatisation incluse</span>
          </div>
          <div class="hero-actions">
            <a class="btn" href="contact.php">Demander une disponibilité</a>
            <a class="btn-secondary" href="disponibilites.php">Voir le planning</a>
          </div>
          <div class="hero-note">
            <strong>Navigation simplifiée :</strong> chaque rubrique a maintenant sa propre page pour une lecture plus claire.
          </div>
        </div>

        <div class="hero-card" role="region" aria-label="Infos rapides">
          <div class="quick">
            <div class="quick-item">
              <div class="quick-label">Email</div>
              <a class="quick-value" href="mailto:hinano.hotel@gmail.com">hinano.hotel@gmail.com</a>
            </div>
            <div class="quick-item">
              <div class="quick-label">Téléphone</div>
              <a class="quick-value" href="tel:+68940661313">+689 40 66 13 13</a><br>
              <a class="quick-value" href="tel:+68987708240">+689 87 70 82 40</a>
            </div>
            <div class="quick-item">
              <div class="quick-label">Adresse</div>
              <div class="quick-value">BP 2032 • 98735 Uturoa</div>
            </div>
          </div>

          <div class="divider"></div>

          <div class="mini-map">
            <div class="mini-map-title">Localisation</div>
            <p class="muted">Centre d’Uturoa • proche commerces, marché couvert, navettes Taha’a.</p>
            <a class="btn-tertiary" target="_blank" rel="noopener"
               href="https://www.google.com/maps/search/?api=1&query=Hotel+Hinano+Uturoa+Raiatea">
              Ouvrir sur Google Maps
            </a>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="section-head compact">
          <div>
            <h2>Accès rapide</h2>
            <p class="lead">Chaque partie importante du site est maintenant séparée pour être plus simple à lire sur téléphone comme sur ordinateur.</p>
          </div>
        </div>

        <div class="cards cards-links">
          <a class="card card-link" href="chambres.php">
            <h3>Chambres</h3>
            <p class="muted">Voir les équipements, le confort, les types de chambres et l’ambiance générale.</p>
            <span class="link-arrow">Ouvrir →</span>
          </a>
          <a class="card card-link" href="services.php">
            <h3>Services</h3>
            <p class="muted">Petit-déjeuner, navette, linge et services utiles pendant le séjour.</p>
            <span class="link-arrow">Ouvrir →</span>
          </a>
          <a class="card card-link" href="tarifs.php">
            <h3>Tarifs</h3>
            <p class="muted">Consulter les prix indicatifs, suppléments et informations pratiques.</p>
            <span class="link-arrow">Ouvrir →</span>
          </a>
          <a class="card card-link" href="disponibilites.php">
            <h3>Planning</h3>
            <p class="muted">Consulter rapidement les disponibilités chambre par chambre en lecture seule.</p>
            <span class="link-arrow">Ouvrir →</span>
          </a>
          <a class="card card-link" href="contact.php">
            <h3>Contact</h3>
            <p class="muted">Appeler, écrire un email ou préparer une demande rapide de réservation.</p>
            <span class="link-arrow">Ouvrir →</span>
          </a>
          <a class="card card-link highlight" href="login.php">
            <h3>Espace pro</h3>
            <p class="muted">Accès réservé à la gérance et aux employés pour le planning complet.</p>
            <span class="link-arrow">Ouvrir →</span>
          </a>
        </div>
      </div>
    </section>
  </main>
<?php public_footer(false); ?>
