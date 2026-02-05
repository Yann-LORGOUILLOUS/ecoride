<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-4 my-lg-5">

  <div class="p-3 p-lg-4 rounded-4 bg-light border text-center mb-3">
    <h1 class="h3 mb-0 fw-bold">DASHBOARD MODERATEUR</h1>
  </div>

  <h2 class="h6 fw-bold text-center mb-4">
    LES TRAJETS A VALIDER (<?= (int)($pendingTripsCount ?? 0) ?>)
  </h2>

  <div class="d-flex flex-column gap-3">

    <?php if (empty($pendingTrips)): ?>
      <div class="alert alert-secondary rounded-4 mb-0">
        Aucun trajet en attente pour le moment.
      </div>
    <?php else: ?>

      <?php foreach ($pendingTrips as $trip): ?>
        <?php
          $createdAtRaw = $trip['created_at'] ?? null;
          $departureRaw = $trip['departure_datetime'] ?? null;

          $createdAt = $createdAtRaw ? (new DateTime($createdAtRaw))->format('d/m/Y H:i') : '—';
          $departureAt = $departureRaw ? (new DateTime($departureRaw))->format('d/m/Y H:i') : '—';

          $driverPseudo = (string)($trip['driver_pseudo'] ?? '');
          $tripId = (int)($trip['id'] ?? 0);
        ?>

        <div class="bg-light border rounded-4 p-3 p-lg-4">
          <div class="row g-2 g-lg-3 align-items-stretch">
            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-pill px-3 py-2 text-center fw-semibold">
                DATE DE CREATION<br>
                <span class="fw-normal"><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-pill px-3 py-2 text-center fw-semibold">
                CONDUCTEUR :<br>
                <span class="fw-normal"><?= htmlspecialchars($driverPseudo, ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-pill px-3 py-2 text-center fw-semibold">
                DATE DU TRAJET<br>
                <span class="fw-normal"><?= htmlspecialchars($departureAt, ENT_QUOTES, 'UTF-8') ?></span>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <a class="btn btn-ecoride-primary fw-bold w-100 h-100 d-flex align-items-center justify-content-center rounded-pill"
                 href="<?= BASE_URL ?>/valider-trajet?trip_id=<?= $tripId ?>">
                DETAILS &amp;<br>VALIDATION
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

    <?php endif; ?>

  </div>

  <div class="text-center mt-4">
    <a class="text-secondary text-decoration-none fw-semibold" href="<?= BASE_URL ?>/dashboard-moderateur">
      &larr; retour au dashboard
    </a>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
