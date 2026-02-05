<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-4 my-lg-5">

  <div class="p-3 p-lg-4 rounded-4 bg-light border text-center mb-4">
    <h1 class="h3 mb-0 fw-bold"><?= htmlspecialchars($pageTitle ?? 'DASHBOARD MODERATEUR') ?></h1>
  </div>

  <div class="row g-3 g-lg-4">

    <div class="col-12 col-lg-4">
      <div class="card h-100 rounded-4 shadow-sm border-0">
        <div class="card-body p-4 d-flex flex-column">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="h5 fw-bold mb-0">Trajets à valider</h2>
            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
              <?= (int)($pendingTripsCount ?? 0) ?>
            </span>
          </div>

          <p class="text-secondary mb-4">
            Valider les trajets en attente : ils passent de <strong>pending</strong> à <strong>planned</strong>
            et deviennent visibles sur la page “Trajets”.
          </p>

          <a class="btn btn-ecoride-primary fw-bold mt-auto"
             href="<?= BASE_URL ?>/liste-trajets-a-valider">
            Accéder
          </a>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card h-100 rounded-4 shadow-sm border-0">
        <div class="card-body p-4 d-flex flex-column">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="h5 fw-bold mb-0">Avis à valider</h2>
            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
              <?= (int)($pendingReviewsCount ?? 0) ?>
            </span>
          </div>

          <p class="text-secondary mb-4">
            Valider les avis déposés : ils sont ensuite pris en compte dans les profils utilisateurs.
          </p>

          <a class="btn btn-ecoride-primary fw-bold mt-auto"
             href="<?= BASE_URL ?>/valider-avis">
            Accéder
          </a>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card h-100 rounded-4 shadow-sm border-0">
        <div class="card-body p-4 d-flex flex-column">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h2 class="h5 fw-bold mb-0">Signalements à traiter</h2>
            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
              <?= (int)($openTripIncidentsCount ?? 0) ?>
            </span>
          </div>

          <p class="text-secondary mb-4">
            Traiter les signalements (MongoDB) : une fois validés, ils sont attachés au compte utilisateur
            (visible pour l’admin seulement).
          </p>

          <a class="btn btn-ecoride-primary fw-bold mt-auto"
             href="<?= BASE_URL ?>/gestion-signalements-trajets">
            Accéder
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
