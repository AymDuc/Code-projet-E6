<?php require_once __DIR__.'/inc_public.php'; public_header('Contact', 'contact'); ?>
<main>
  <section class="page-hero">
    <div class="wrap narrow">
      <span class="eyebrow">Contact</span>
      <h1>Nous contacter facilement</h1>
      <p class="lead">Par téléphone, par email, ou via une demande rapide pour préparer ton message.</p>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <div class="contact-grid">
        <div class="card">
          <h3>Nous joindre</h3>
          <p class="muted">
            Email : <a href="mailto:hinano.hotel@gmail.com">hinano.hotel@gmail.com</a><br>
            Téléphone : <a href="tel:+68940661313">+689 40 66 13 13</a> / <a href="tel:+68987708240">+689 87 70 82 40</a>
          </p>
          <p class="muted">Adresse : BP 2032 • 98735 Uturoa</p>
          <a class="btn" href="mailto:hinano.hotel@gmail.com?subject=Demande%20de%20disponibilit%C3%A9%20-%20H%C3%B4tel%20Hinano">Envoyer un email</a>
        </div>

        <form class="card form" id="contact-mail-form">
          <h3>Demande rapide</h3>
          <label>Nom
            <input name="Nom" required placeholder="Ton nom">
          </label>
          <label>Téléphone / Email
            <input name="Contact" required placeholder="+689... / email">
          </label>
          <label>Dates souhaitées
            <input name="Dates" required placeholder="ex: 12 au 15 mars">
          </label>
          <label>Message
            <textarea name="Message" rows="4" placeholder="Nb personnes, chambre demandée, etc."></textarea>
          </label>
          <button class="btn" type="submit">Préparer l’email</button>
          <div class="muted small">Le bouton prépare un vrai email propre sans renvoyer vers une page cassée.</div>
          <div class="muted small" id="contact-mail-status" aria-live="polite"></div>
        </form>
        <script>
        (function () {
          const form = document.getElementById('contact-mail-form');
          if (!form) return;

          form.addEventListener('submit', function (e) {
            e.preventDefault();

            const nom = (form.elements['Nom']?.value || '').trim();
            const contact = (form.elements['Contact']?.value || '').trim();
            const dates = (form.elements['Dates']?.value || '').trim();
            const message = (form.elements['Message']?.value || '').trim();
            const status = document.getElementById('contact-mail-status');

            if (!nom || !contact || !dates) {
              if (status) status.textContent = 'Merci de remplir le nom, le contact et les dates.';
              return;
            }

            const subject = 'Demande de disponibilité - Hôtel Hinano';
            const body = [
              'Bonjour,',
              '',
              'Je souhaite faire une demande de disponibilité.',
              '',
              'Nom : ' + nom,
              'Contact : ' + contact,
              'Dates souhaitées : ' + dates,
              'Message : ' + (message || '-'),
              '',
              'Merci.'
            ].join('\n');

            const mailto = 'mailto:hinano.hotel@gmail.com'
              + '?subject=' + encodeURIComponent(subject)
              + '&body=' + encodeURIComponent(body);

            window.open(mailto, '_self');

            if (status) {
              status.textContent = 'Si rien ne s’ouvre, vérifie qu’une application mail est configurée sur l’appareil.';
            }
          });
        })();
        </script>
      </div>
    </div>
  </section>
</main>
<?php public_footer(false); ?>
