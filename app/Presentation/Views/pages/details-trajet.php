<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$trip = $trip ?? [];
$driverRating = $driverRating ?? ['avg' => 0.0, 'count' => 0];
$driverReviews = $driverReviews ?? [];
$depCity = (string)($trip['city_from'] ?? '');
$arrCity = (string)($trip['city_to'] ?? '');
$depAt = (string)($trip['departure_datetime'] ?? '');
$arrAt = (string)($trip['arrival_datetime'] ?? '');
$price = (int)($trip['price_credits'] ?? 0);
$seatsAvailable = (int)($trip['seats_available'] ?? 0);
$driverPseudo = (string)($trip['driver_pseudo'] ?? '');
$driverAvatar = (string)($trip['driver_avatar_url'] ?? '');
$driverCreatedAt = (string)($trip['driver_created_at'] ?? '');
$driverTripsCount = (int)($trip['driver_trips_count'] ?? 0);
$vehicleBrand = (string)($trip['vehicle_brand'] ?? '');
$vehicleModel = (string)($trip['vehicle_model'] ?? '');
$vehicleEnergy = (string)($trip['vehicle_energy'] ?? '');
$vehicleSeatsTotal = (int)($trip['vehicle_seats_total'] ?? 0);
$smokingAllowed = (int)($trip['smoking_allowed'] ?? 0) === 1;
$petsAllowed = (int)($trip['pets_allowed'] ?? 0) === 1;
$description = (string)($trip['driver_notes'] ?? '');
$isEco = $vehicleEnergy === 'electric';
$energyLabel = match ($vehicleEnergy) {
    'electric' => 'Électrique',
    'hybrid' => 'Hybride',
    'fuel' => 'Thermique',
    default => 'Inconnu',
};
$ratingAvg = (float)($driverRating['avg'] ?? 0.0);
$ratingCount = (int)($driverRating['count'] ?? 0);
$stars = (int)round($ratingAvg);
$driverInitial = $driverPseudo !== '' ? mb_strtoupper(mb_substr($driverPseudo, 0, 1)) : '?';
$fmtDateTime = function (string $dt): string {
    if ($dt === '') return '';
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y H:i', $ts) : $dt;
};
$fmtDate = function (string $dt): string {
    if ($dt === '') return '';
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y', $ts) : $dt;
};
$smokingLabel = $smokingAllowed ? 'Véhicule fumeur' : 'Véhicule non-fumeur';
$petsLabel = $petsAllowed ? 'Animaux acceptés' : 'Animaux refusés';
$vehicleLabel = trim($vehicleBrand . ' ' . $vehicleModel);
?>

<div class="container-xxl px-3 py-4">

  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body py-4">
      <div class="d-flex flex-column align-items-center text-center gap-3">

        <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap">
          <div class="px-3">
            <div class="fw-bold display-6 mb-1"><?= htmlspecialchars($depCity) ?></div>
            <div class="fs-5 text-secondary"><?= htmlspecialchars($fmtDateTime($depAt)) ?></div>
          </div>

          <div class="fs-1 text-secondary fw-semibold">→</div>

          <div class="px-3">
            <div class="fw-bold display-6 mb-1"><?= htmlspecialchars($arrCity) ?></div>
            <div class="fs-5 text-secondary"><?= htmlspecialchars($fmtDateTime($arrAt)) ?></div>
          </div>
        </div>

        <span class="badge rounded-pill px-4 py-2 <?= $isEco ? 'text-bg-success' : 'text-bg-secondary' ?>">
          <?= $isEco ? 'Trajet écologique 🌱' : 'Trajet standard' ?>
        </span>

      </div>
    </div>
  </div>

  <div class="text-center mb-3">
    <span class="badge rounded-pill px-4 py-2 text-bg-light fw-semibold border">
        CONDUCTEUR
    </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-0">

        <div class="p-4">
        <div class="row g-4 align-items-center">

            <div class="col-12 col-lg-4 text-center">
            <?php if ($driverAvatar !== ''): ?>
                <img
                src="<?= htmlspecialchars($driverAvatar) ?>"
                alt="Photo du conducteur"
                width="190"
                height="190"
                class="rounded-circle border shadow-sm object-fit-cover"
                >
            <?php else: ?>
                <div
                class="rounded-circle border shadow-sm d-flex align-items-center justify-content-center bg-white mx-auto"
                style="width:190px;height:190px;"
                >
                <span class="display-3 fw-semibold text-secondary"><?= htmlspecialchars($driverInitial) ?></span>
                </div>
            <?php endif; ?>
            </div>

            <div class="col-12 col-lg-8">
            <div class="d-flex flex-column gap-3">

                <div>
                <div class="text-secondary fw-semibold">Pseudo</div>
                <div class="display-6 fw-bold lh-1">
                    <?= htmlspecialchars($driverPseudo) ?>
                </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                <span class="badge rounded-pill text-bg-light border px-3 py-2">
                    <span class="text-secondary">Inscrit le</span>
                    <span class="fw-semibold ms-1"><?= htmlspecialchars($fmtDate($driverCreatedAt)) ?></span>
                </span>

                <span class="badge rounded-pill text-bg-light border px-3 py-2">
                    <span class="text-secondary">Trajets réalisés</span>
                    <span class="fw-semibold ms-1"><?= (int)$driverTripsCount ?></span>
                </span>
                </div>

                <div class="border rounded-4 p-3 bg-body-tertiary">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                    <div class="fw-bold mb-1">NOTE MOYENNE</div>
                    <div class="fs-3" aria-label="Note moyenne du conducteur">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span aria-hidden="true"><?= $i <= $stars ? '★' : '☆' ?></span>
                        <?php endfor; ?>
                    </div>
                    <div class="text-secondary">
                        <?= (int)$ratingCount ?> avis validés
                    </div>
                    </div>

                    <a class="btn btn-ecoride-primary rounded-pill px-4"
                    href="<?= BASE_URL ?>/profils?id=<?= (int)$trip['driver_id'] ?>#avis">
                    Voir les avis
                    </a>
                </div>
                </div>

            </div>
            </div>

        </div>
        </div>

    </div>
    </div>

  <div class="row g-4 align-items-stretch mb-4">
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
          <div class="fw-bold fs-4 mb-3">Préférences</div>

          <div class="fs-5 mb-2">
            <span class="text-secondary">Animaux :</span>
            <span class="fw-semibold ms-2"><?= htmlspecialchars($petsLabel) ?></span>
          </div>

          <div class="fs-5">
            <span class="text-secondary">Fumeur :</span>
            <span class="fw-semibold ms-2"><?= htmlspecialchars($smokingLabel) ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
          <div class="fw-bold fs-4 mb-3">Description du trajet :</div>

          <div class="border rounded-4 p-3 bg-body-tertiary" style="min-height: 150px;">
            <?= $description !== '' ? nl2br(htmlspecialchars($description)) : '<span class="text-secondary">Aucune description.</span>' ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-4">
          <div class="fw-bold fs-4 mb-1">Véhicule / Énergie</div>
          <div class="fs-5 text-secondary mb-2"><?= htmlspecialchars($energyLabel) ?></div>
          <div class="fs-4 fw-semibold"><?= htmlspecialchars($vehicleLabel) ?></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-4">
          <div class="fw-bold fs-4 mb-2">Places</div>
          <div class="display-6 fw-bold"><?= (int)$seatsAvailable ?> / <?= (int)$vehicleSeatsTotal ?></div>
          <div class="text-secondary">disponibles</div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-4">
          <div class="fw-bold fs-4 mb-2">Coût</div>
          <div class="display-6 fw-bold"><?= (int)$price ?></div>
          <div class="text-secondary">crédits</div>
        </div>
      </div>
    </div>
  </div>

  <?php $isConnected = isset($_SESSION['user']); ?>
  <div class="card border-0 shadow-sm rounded-4">
  <div class="card-body text-center py-4">

    <?php if ($isConnected): ?>
      <button
        id="reserveBtn"
        type="button"
        class="btn btn-ecoride-primary btn-lg rounded-pill fw-bold px-5"
        data-bs-toggle="modal"
        data-bs-target="#confirmReservationModal"
      >
        RÉSERVER CE TRAJET
      </button>
    <?php else: ?>
      <button
        id="reserveBtn"
        type="button"
        class="btn btn-ecoride-primary btn-lg rounded-pill fw-bold px-5"
        data-bs-toggle="modal"
        data-bs-target="#loginRequiredModal"
      >
        RÉSERVER CE TRAJET
      </button>

      <div class="text-secondary mt-2">
        Vous devez être connecté pour réserver un trajet
      </div>
    <?php endif; ?>

    <div class="mt-3">
      <button
        type="button"
        class="btn btn-outline-secondary rounded-pill px-4"
        onclick="window.history.back()"
      >
        ← Retour à la liste des trajets
      </button>
    </div>

  </div>
</div>

<div class="modal fade" id="confirmReservationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body pt-0">
        Confirmez-vous la réservation de ce trajet ?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
          Annuler
        </button>
        <button type="button" class="btn btn-ecoride-primary rounded-pill px-4" id="confirmReserveYes">
          Oui
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reservationSuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Réservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body pt-0">
        Félicitations ! Trajet réservé.
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-ecoride-primary rounded-pill px-4" data-bs-dismiss="modal">
          OK
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Connexion requise</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body pt-0">
        Il faut être connecté pour réserver un trajet.
        <div class="mt-3 d-grid gap-2">
          <?php $redirect = urlencode($_SERVER['REQUEST_URI']); ?>
          <a class="btn btn-ecoride-primary rounded-pill"
            href="<?= BASE_URL ?>/connexion?redirect=<?= $redirect ?>">
            Déjà inscrit ? Connectez-vous
          </a>
          <a class="btn btn-outline-secondary rounded-pill" href="<?= BASE_URL ?>/inscription">
            Pas encore inscrit ? Inscrivez-vous
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    const yesBtn = document.getElementById('confirmReserveYes');
    const reserveBtn = document.getElementById('reserveBtn');

    if (!yesBtn || !reserveBtn) return;

    yesBtn.addEventListener('click', function () {
      const confirmEl = document.getElementById('confirmReservationModal');
      const successEl = document.getElementById('reservationSuccessModal');

      if (!confirmEl || !successEl) return;

      const confirmModal = bootstrap.Modal.getOrCreateInstance(confirmEl);
      const successModal = bootstrap.Modal.getOrCreateInstance(successEl);

      confirmModal.hide();
      successModal.show();

      reserveBtn.disabled = true;
      reserveBtn.classList.add('disabled');
      reserveBtn.setAttribute('aria-disabled', 'true');
    });
  })();
</script>


<?php require __DIR__ . '/../layouts/footer.php'; ?>
