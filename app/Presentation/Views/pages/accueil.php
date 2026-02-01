<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xxl px-3">

  <section class="mb-4">
    <form class="bg-body rounded-4 p-3 border shadow-sm" method="get" action="<?= BASE_URL ?>/trajets">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-lg">
          <input class="form-control form-control-lg rounded-pill text-center" name="from" type="text" placeholder="Ville de départ">
        </div>
        <div class="col-12 col-lg">
          <input class="form-control form-control-lg rounded-pill text-center" name="to" type="text" placeholder="Ville d'arrivée">
        </div>
        <div class="col-12 col-lg-3">
          <input class="form-control form-control-lg rounded-pill text-center" name="date" type="date">
        </div>
        <div class="col-12 col-lg-auto d-grid">
          <button class="btn btn-ecoride-primary btn-lg rounded-pill fw-bold" type="submit">
            LANCER LA RECHERCHE
          </button>
        </div>
      </div>
    </form>
  </section>

  <section class="mb-4">
    <div class="bg-body rounded-4 p-4 border text-center shadow-sm mx-auto" style="max-width: 760px;">
      <h1 class="fw-bold mb-2">EcoRide</h1>
      <p class="text-secondary mb-0">
        Plateforme de covoiturage écologique : moins de CO₂, plus de trajets utiles, plus d’humains.
      </p>
    </div>
  </section>

  <section class="mb-4">
    <div class="bg-body rounded-4 p-4 border shadow-sm">
      <div class="row g-3 align-items-center">
        <div class="col-12 col-lg-5">
          <div class="bg-secondary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center"
               style="height: 260px;">
            <img src="<?= BASE_URL ?>/assets/images/image-presentation.png" alt="Présentation EcoRide" class="w-100 h-100" style="object-fit: cover;">
          </div>
        </div>
        <div class="col-12 col-lg-7">
          <h2 class="fw-bold mb-2">Une alternative simple et responsable</h2>
          <p class="text-secondary mb-0">
            EcoRide facilite la mise en relation entre conducteurs et passagers, avec une logique de
            communauté, de confiance (avis) et un système de crédits pour encourager les bons comportements.
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="mb-4">
    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <article class="bg-body rounded-4 p-4 border shadow-sm text-center h-100">
          <h3 class="fw-bold mb-2">Rechercher un trajet</h3>
          <p class="text-secondary">Trouve rapidement un covoiturage adapté à ton besoin.</p>
          <div><img src="<?= BASE_URL ?>/assets/images/image-chercher-trajet.png" alt="Rechercher un trajet" class="w-100 h-100" style="object-fit: cover;"></div>
          <a class="btn btn-outline-secondary rounded-pill px-4" href="<?= BASE_URL ?>/trajets">Voir les trajets</a>
        </article>
      </div>
      <div class="col-12 col-lg-4">
        <article class="bg-body rounded-4 p-4 border shadow-sm text-center h-100">
          <h3 class="fw-bold mb-2">Proposer un trajet</h3>
          <p class="text-secondary">Publie ton trajet et partage les frais en toute simplicité.</p>
          <div><img src="<?= BASE_URL ?>/assets/images/image-proposer-trajet.png" alt="Proposer un trajet" class="w-100 h-100" style="object-fit: cover;"></div>
          <a class="btn btn-outline-secondary rounded-pill px-4" href="<?= BASE_URL ?>/creer-trajet">Proposer un trajet</a>
        </article>
      </div>
      <div class="col-12 col-lg-4">
        <article class="bg-body rounded-4 p-4 border shadow-sm text-center h-100">
          <h3 class="fw-bold mb-2">Laisser un avis</h3>
          <p class="text-secondary">Partage ton expérience et aide la communauté.</p>
          <div><img src="<?= BASE_URL ?>/assets/images/image-laisser-avis.png" alt="Laisser un avis" class="w-100 h-100" style="object-fit: cover;"></div>
          <a class="btn btn-outline-secondary rounded-pill px-4" href="<?= BASE_URL ?>/rediger-avis">Voir les avis</a>
        </article>
      </div>
    </div>
  </section>

  <section class="mb-2">
  <div class="text-center mb-3">
    <h2 class="fw-bold mb-1">Avis de la communauté</h2>
    <p class="text-secondary mb-0">Ils utilisent EcoRide et racontent leur expérience.</p>
  </div>

  <div class="container">
    <div class="row align-items-center">
      <div class="col-1 d-flex align-items-center justify-content-center">
        <button class="btn-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
      </div>

      <div class="col-10">
        <div id="carouselExample" class="carousel slide">
          <div class="carousel-inner">

            <div class="carousel-item active">
              <div class="row p-3 g-3">
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Application très intuitive, j’ai réservé mon covoiturage en quelques clics.”
                    </p>
                    <p class="review-meta">
                      <strong>Camille</strong><br>5/5
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Interface claire et agréable, exactement ce qu’il me fallait pour mes trajets quotidiens.”
                    </p>
                    <p class="review-meta">
                      <strong>Lassina</strong><br>5/5
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “J’ai trouvé un trajet rapidement, et l’expérience utilisateur est top.”
                    </p>
                    <p class="review-meta">
                      <strong>Sophie</strong><br>5/5
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="carousel-item">
              <div class="row p-3 g-3">
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “EcoRide rend le covoiturage simple et accessible, je recommande sans hésiter.”
                    </p>
                    <p class="review-meta">
                      <strong>Luka</strong><br>5/5
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Très bonne surprise, tout est fluide et bien pensé.”
                    </p>
                    <p class="review-meta">
                      <strong>Élodie</strong><br>4/5
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Une plateforme moderne et efficace, parfaite pour organiser mes déplacements.”
                    </p>
                    <p class="review-meta">
                      <strong>Miljan</strong><br>5/5
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="carousel-item">
              <div class="row p-3 g-3">
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Recherche rapide, design agréable et navigation intuitive.”
                    </p>
                    <p class="review-meta">
                      <strong>Maria</strong><br>5/5
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Enfin une application de covoiturage simple à utiliser et bien pensée.”
                    </p>
                    <p class="review-meta">
                      <strong>Antoine</strong><br>5/5
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="review-circle mx-auto">
                    <p class="review-text">
                      “Tout est clair dès la première utilisation, c’est vraiment agréable.”
                    </p>
                    <p class="review-meta">
                      <strong>Jaya</strong><br>5/5
                    </p>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </div>
      </div>

      <div class="col-1 d-flex align-items-center justify-content-center">
        <button class="btn-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
