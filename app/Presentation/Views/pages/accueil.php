<?php require __DIR__ . '/../layouts/header.php'; ?>

<section class="home">

  <section class="home-search" aria-label="Recherche rapide de trajet">
    <div class="container">
      <form class="search-bar" method="get" action="<?= BASE_URL ?>/trajets">
        <label class="sr-only" for="from">Ville de départ</label>
        <input id="from" name="from" type="text" placeholder="Ville de départ">

        <label class="sr-only" for="to">Ville d'arrivée</label>
        <input id="to" name="to" type="text" placeholder="Ville d'arrivée">

        <label class="sr-only" for="date">Date</label>
        <input id="date" name="date" type="date">

        <button class="btn-primary" type="submit">Lancer la recherche</button>
      </form>
    </div>
  </section>

  <section class="home-hero" aria-label="Présentation EcoRide">
    <div class="container">
      <div class="hero-card">
        <h1 class="hero-title">EcoRide</h1>
        <p class="hero-subtitle">
          Plateforme de covoiturage écologique : des trajets partagés, moins de CO₂, plus de convivialité.
        </p>
      </div>
    </div>
  </section>

  <section class="home-about" aria-label="Présentation entreprise">
    <div class="container">
      <div class="about-card">
        <div class="about-media" aria-hidden="true">
          <div class="media-placeholder">PHOTOS</div>
        </div>

        <div class="about-content">
          <h2>Une alternative simple et responsable</h2>
          <p>
            EcoRide facilite la mise en relation entre conducteurs et passagers, avec un système de crédits
            pour encourager les comportements responsables et valoriser la qualité des trajets.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="home-actions" aria-label="Actions principales">
    <div class="container">
      <div class="actions-grid">
        <article class="action-card">
          <h3>Rechercher un trajet</h3>
          <p>Trouve rapidement un covoiturage adapté à tes besoins.</p>
          <a class="btn-secondary" href="<?= BASE_URL ?>/trajets">Voir les trajets</a>
        </article>

        <article class="action-card">
          <h3>Proposer un trajet</h3>
          <p>Publie ton trajet et partage les frais en toute simplicité.</p>
          <a class="btn-secondary" href="<?= BASE_URL ?>/creer-trajet">Proposer un trajet</a>
        </article>

        <article class="action-card">
          <h3>Laisser un avis</h3>
          <p>Partage ton expérience et aide la communauté à choisir.</p>
          <a class="btn-secondary" href="<?= BASE_URL ?>/rediger-avis">Voir les avis</a>
        </article>
      </div>
    </div>
  </section>

  <section class="home-reviews" aria-label="Avis utilisateurs">
    <div class="container">
      <div class="reviews-header">
        <h2>Avis de la communauté</h2>
        <p>Ils utilisent EcoRide et racontent leur expérience.</p>
      </div>

      <div class="reviews-carousel">
        <button class="carousel-arrow" type="button" aria-label="Avis précédent">‹</button>

        <div class="reviews-track">
          <article class="review-card">
            <p class="review-text">“Super simple, j’ai trouvé un trajet en 2 minutes.”</p>
            <p class="review-meta"><span class="review-name">Camille</span> · 5/5</p>
          </article>

          <article class="review-card">
            <p class="review-text">“Conduire avec EcoRide, c’est fluide et bien présenté.”</p>
            <p class="review-meta"><span class="review-name">Rayan</span> · 5/5</p>
          </article>

          <article class="review-card">
            <p class="review-text">“Les avis donnent confiance, et le système de crédits est malin.”</p>
            <p class="review-meta"><span class="review-name">Sofia</span> · 4/5</p>
          </article>
        </div>

        <button class="carousel-arrow" type="button" aria-label="Avis suivant">›</button>
      </div>

      <div class="carousel-dots" aria-hidden="true">
        <span class="dot is-active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>
  </section>

</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>