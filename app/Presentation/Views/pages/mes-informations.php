<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
$user = $user ?? [];
$driverTrips = $driverTrips ?? [];
$passengerReservations = $passengerReservations ?? [];
$pseudo = (string)($user['pseudo'] ?? '');
$lastName = (string)($user['last_name'] ?? '');
$firstName = (string)($user['first_name'] ?? '');
$email = (string)($user['email'] ?? '');
$avatarUrl = (string)($user['avatar_url'] ?? '');
?>

<div class="container-xxl px-3 py-4">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <h1 class="fw-bold mb-0">MES INFORMATIONS</h1>
    <a class="btn btn-light border fw-semibold" href="<?= BASE_URL ?>/mon-compte">Retour dashboard</a>
  </div>

  <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
      <?= htmlspecialchars((string)$flash['message']) ?>
    </div>
  <?php endif; ?>

  <div class="p-3 p-md-4 rounded-4 bg-white border border-black border-opacity-10 mb-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="rounded-circle overflow-hidden border bg-light" style="width:72px;height:72px;">
        <?php if ($avatarUrl !== ''): ?>
          <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
        <?php else: ?>
          <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary small">
            N/A
          </div>
        <?php endif; ?>
      </div>

      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <div class="fw-bold fs-5"><?= htmlspecialchars($pseudo) ?></div>
          <span class="text-secondary">•</span>
          <div class="text-secondary"><?= htmlspecialchars($email) ?></div>
        </div>
        <div class="text-secondary small">
          <?= htmlspecialchars($firstName) ?> <?= htmlspecialchars($lastName) ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 align-items-stretch">

    <div class="col-12 col-lg-6">
      <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100">
        <h2 class="h5 fw-bold mb-3">Modifier mes informations</h2>

        <form method="post" action="<?= BASE_URL ?>/mes-informations" class="d-grid gap-3" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
          <input type="hidden" name="action" value="update_profile">

          <div>
            <label class="form-label fw-semibold" for="pseudo">Pseudo</label>
            <input class="form-control rounded-4" id="pseudo" name="pseudo" value="<?= htmlspecialchars($pseudo) ?>" required>
          </div>

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold" for="last_name">Nom</label>
              <input class="form-control rounded-4" id="last_name" name="last_name" value="<?= htmlspecialchars($lastName) ?>" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold" for="first_name">Prénom</label>
              <input class="form-control rounded-4" id="first_name" name="first_name" value="<?= htmlspecialchars($firstName) ?>" required>
            </div>
          </div>

          <div>
            <label class="form-label fw-semibold" for="email">Email</label>
            <input class="form-control rounded-4" id="email" name="email" type="email" value="<?= htmlspecialchars($email) ?>" required>
          </div>

          <div>
            <label class="form-label fw-semibold" for="avatar_url">Avatar (URL)</label>
            <input class="form-control rounded-4" id="avatar_url" name="avatar_url" value="<?= htmlspecialchars($avatarUrl) ?>" placeholder="https://...">
          </div>

          <div class="pt-2">
            <button class="btn btn-ecoride-primary fw-bold rounded-pill w-100" type="submit">
              Enregistrer mes informations
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-6">
    <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100 d-flex flex-column">

        <h2 class="h5 fw-bold mb-3">Modifier mon mot de passe</h2>

        <form method="post"
            action="<?= BASE_URL ?>/mes-informations"
            class="d-flex flex-column flex-grow-1"
            novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="action" value="update_password">

        <div class="mb-3">
            <label class="form-label fw-semibold" for="current_password">Ancien mot de passe</label>
            <input class="form-control rounded-4" id="current_password" name="current_password" type="password" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="new_password">Nouveau mot de passe</label>
            <input class="form-control rounded-4" id="new_password" name="new_password" type="password" minlength="8" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="new_password_confirm">Confirmer le nouveau mot de passe</label>
            <input class="form-control rounded-4" id="new_password_confirm" name="new_password_confirm" type="password" minlength="8" required>
        </div>

        <div class="mt-auto pt-2">
            <button class="btn btn-ecoride-primary fw-bold rounded-pill w-100" type="submit">
            Mettre à jour le mot de passe
            </button>
        </div>

        </form>
    </div>
    </div>

  </div>

  <div id="myTripsDriver" class="row g-3 mt-3">
    <div class="col-12">
      <div class="p-4 rounded-4 bg-white border border-black border-opacity-10">
        <h2 class="h5 fw-bold mb-3">Mes covoiturages (chauffeur)</h2>

        <?php if (empty($driverTrips)): ?>
          <p class="text-secondary mb-0">Aucun covoiturage proposé / prévu / en cours.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr class="text-secondary small">
                  <th>Trajet</th>
                  <th>Départ</th>
                  <th>Arrivée</th>
                  <th>Statut</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($driverTrips as $t): ?>
                  <?php
                    $tripId = (int)($t['id'] ?? 0);
                    $status = (string)($t['status'] ?? '');
                    $fromTo = (string)($t['city_from'] ?? '') . ' → ' . (string)($t['city_to'] ?? '');
                  ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($fromTo, ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($t['departure_datetime'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($t['arrival_datetime'] ?? ''), ENT_QUOTES) ?></td>
                    <td>
                      <span class="badge text-bg-light border">
                        <?= htmlspecialchars($status, ENT_QUOTES) ?>
                      </span>
                    </td>
                    <td class="text-end">
                      <div class="d-inline-flex gap-2 flex-wrap justify-content-end">

                        <?php if (in_array($status, ['pending','planned'], true)): ?>
                          <form method="post" action="<?= BASE_URL ?>/mes-informations">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="action" value="cancel_trip">
                            <input type="hidden" name="trip_id" value="<?= $tripId ?>">
                            <button class="btn btn-light border rounded-pill fw-semibold" type="submit">
                              Annuler
                            </button>
                          </form>
                        <?php endif; ?>

                        <?php if ($status === 'planned'): ?>
                          <form method="post" action="<?= BASE_URL ?>/mes-informations">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="action" value="start_trip">
                            <input type="hidden" name="trip_id" value="<?= $tripId ?>">
                            <button class="btn btn-ecoride-primary rounded-pill fw-bold" type="submit">
                              Démarrer
                            </button>
                          </form>
                        <?php endif; ?>

                        <?php if ($status === 'ongoing'): ?>
                          <form method="post" action="<?= BASE_URL ?>/mes-informations">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                            <input type="hidden" name="action" value="finish_trip">
                            <input type="hidden" name="trip_id" value="<?= $tripId ?>">
                            <button class="btn btn-ecoride-primary rounded-pill fw-bold" type="submit">
                              Clôturer
                            </button>
                          </form>
                        <?php endif; ?>

                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <p class="text-secondary small mb-0">
            Annuler et Démarrer notifient automatiquement les passagers. Clôturer invite les passagers à laisser un avis.
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div id="myTripsPassenger" class="row g-3 mt-3">
    <div class="col-12">
      <div class="p-4 rounded-4 bg-white border border-black border-opacity-10">
        <h2 class="h5 fw-bold mb-3">Mes réservations (passager)</h2>

        <?php if (empty($passengerReservations)): ?>
          <p class="text-secondary mb-0">Aucune réservation confirmée.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr class="text-secondary small">
                  <th>Trajet</th>
                  <th>Départ</th>
                  <th>Chauffeur</th>
                  <th>Crédits</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($passengerReservations as $r): ?>
                  <?php
                    $reservationId = (int)($r['reservation_id'] ?? 0);
                    $tripStatus = (string)($r['trip_status'] ?? '');
                    $fromTo = (string)($r['city_from'] ?? '') . ' → ' . (string)($r['city_to'] ?? '');
                    $canCancel = in_array($tripStatus, ['pending', 'planned'], true);
                  ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($fromTo, ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($r['departure_datetime'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($r['driver_pseudo'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= (int)($r['price_credits'] ?? 0) ?></td>
                    <td class="text-end">
                      <?php if ($canCancel): ?>
                        <form method="post" action="<?= BASE_URL ?>/mes-informations" class="d-inline">
                          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                          <input type="hidden" name="action" value="cancel_reservation">
                          <input type="hidden" name="reservation_id" value="<?= $reservationId ?>">
                          <button class="btn btn-light border rounded-pill fw-semibold" type="submit">
                            Annuler
                          </button>
                        </form>
                      <?php else: ?>
                        <span class="text-secondary small">Annulation indisponible</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <p class="text-secondary small mb-0">
            L’annulation rembourse automatiquement les crédits et notifie le chauffeur (si le trajet n’a pas démarré).
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
