<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-xxl px-3 py-4">

  <?php
    $filters = $filters ?? ['from' => '', 'to' => '', 'date' => '', 'sort' => 'date_asc', 'limit' => 12];
    $searched = $searched ?? false;

    $trips = $trips ?? [];
    $currentCount = count($trips);

    $totalTrips = isset($totalTrips) ? (int)$totalTrips : $currentCount;

    $hasMore = $hasMore ?? false;
    $nextLimit = isset($nextLimit) ? (int)$nextLimit : ((int)($filters['limit'] ?? 12) + 12);

    $messageSuffix = $searched ? "trajets correspondent à votre recherche" : "trajets disponibles";
  ?>

  <section class="mb-4">
    <form class="bg-body rounded-4 p-3 border shadow-sm" method="get" action="<?= BASE_URL ?>/trajets">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-lg">
          <input
            class="form-control form-control-lg rounded-pill text-center"
            name="from"
            type="text"
            placeholder="Ville de départ"
            value="<?= htmlspecialchars((string)($filters['from'] ?? '')) ?>"
          >
        </div>
        <div class="col-12 col-lg">
          <input
            class="form-control form-control-lg rounded-pill text-center"
            name="to"
            type="text"
            placeholder="Ville d'arrivée"
            value="<?= htmlspecialchars((string)($filters['to'] ?? '')) ?>"
          >
        </div>
        <div class="col-12 col-lg-3">
          <input
            class="form-control form-control-lg rounded-pill text-center"
            name="date"
            type="date"
            value="<?= htmlspecialchars((string)($filters['date'] ?? '')) ?>"
          >
        </div>
        <div class="col-12 col-lg-auto d-grid">
          <button class="btn btn-ecoride-primary btn-lg rounded-pill fw-bold" type="submit">
            LANCER LA RECHERCHE
          </button>
        </div>
      </div>
    </form>
  </section>

  <?php if ($searched && $totalTrips === 0): ?>
    <section class="mb-4">
      <div class="bg-body rounded-4 p-4 border shadow-sm text-center">
        <h2 class="h5 fw-bold mb-2">Aucun trajet ne correspond à votre recherche</h2>
        <p class="text-secondary mb-0">
          Modifiez les villes ou la date, puis relancez la recherche.
        </p>
      </div>
    </section>

  <?php else: ?>
    <section class="mb-3">
      <div class="bg-body rounded-pill px-3 py-2 border shadow-sm text-center">
        <span class="fw-bold"><?= $totalTrips ?></span>
        <span class="fw-semibold"><?= htmlspecialchars($messageSuffix) ?></span>
      </div>
    </section>

    <?php if ($currentCount > 0): ?>
      <section class="row g-3 mb-4">
        <?php foreach ($trips as $trip): ?>
          <?php
            $fromCity = (string)($trip['city_from'] ?? '');
            $toCity = (string)($trip['city_to'] ?? '');

            $departure = (string)($trip['departure_datetime'] ?? '');
            $arrival = (string)($trip['arrival_datetime'] ?? '');

            $driver = (string)($trip['driver_pseudo'] ?? '');
            $seats = (int)($trip['seats_available'] ?? 0);
            $credits = (int)($trip['price_credits'] ?? 0);

            $energy = (string)($trip['vehicle_energy'] ?? '');
            $eco = in_array(strtolower($energy), ['electric', 'hybrid', 'electrique', 'hybride'], true);

            $tripId = (int)($trip['id'] ?? 0);
          ?>

          <div class="col-12 col-lg-4">
            <article class="bg-body rounded-4 p-3 border shadow-sm h-100">

              <div class="bg-secondary bg-opacity-10 rounded-pill px-3 py-2 fw-semibold text-center mb-2">
                <span><?= htmlspecialchars($fromCity) ?></span>
                <span class="text-secondary mx-2">→</span>
                <span><?= htmlspecialchars($toCity) ?></span>
              </div>

              <div class="d-flex flex-column flex-sm-row gap-2 mb-2">
                <div class="flex-fill bg-secondary bg-opacity-10 rounded-pill px-3 py-2 small text-center">
                  Date/Heure départ
                  <div class="fw-semibold"><?= htmlspecialchars($departure) ?></div>
                </div>
                <div class="flex-fill bg-secondary bg-opacity-10 rounded-pill px-3 py-2 small text-center">
                  Date/Heure arrivée
                  <div class="fw-semibold"><?= htmlspecialchars($arrival) ?></div>
                </div>
              </div>

              <div class="bg-secondary bg-opacity-10 rounded-pill px-3 py-2 small text-center mb-2">
                Conducteur : <span class="fw-semibold"><?= htmlspecialchars($driver) ?></span>
              </div>

              <div class="bg-secondary bg-opacity-10 rounded-pill px-3 py-2 small text-center mb-2">
                Places disponibles : <span class="fw-semibold"><?= $seats ?></span>
              </div>

              <div class="bg-secondary bg-opacity-10 rounded-pill px-3 py-2 small text-center mb-2">
                Prix : <span class="fw-semibold"><?= $credits ?></span> crédits
              </div>

              <div class="bg-secondary bg-opacity-10 rounded-pill px-3 py-2 small text-center mb-3">
                Mention écologique : <span class="fw-semibold"><?= $eco ? 'Oui' : 'Non' ?></span>
              </div>

              <div class="d-grid">
                <?php $back = urlencode($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/trajets')); ?>
                <a class="btn btn-ecoride-primary rounded-pill fw-semibold"
                  href="<?= BASE_URL ?>/details-trajet?id=<?= $tripId ?>&back=<?= $back ?>">
                  Voir le détail
                </a>
              </div>

            </article>
          </div>
        <?php endforeach; ?>
      </section>

      <?php if ($hasMore): ?>
        <?php
          $query = http_build_query([
            'from'  => (string)($filters['from'] ?? ''),
            'to'    => (string)($filters['to'] ?? ''),
            'date'  => (string)($filters['date'] ?? ''),
            'sort'  => (string)($filters['sort'] ?? 'date_asc'),
            'limit' => (int)$nextLimit,
          ]);

          $moreUrl = BASE_URL . '/trajets?' . $query;
        ?>

        <div class="text-center mt-4 mb-4">
          <a class="btn btn-outline-success rounded-pill fw-bold px-4" href="<?= htmlspecialchars($moreUrl) ?>">
            Voir plus
          </a>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
