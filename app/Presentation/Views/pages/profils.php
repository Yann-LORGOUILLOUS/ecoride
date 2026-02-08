<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$q = $q ?? '';
$searchResults = $searchResults ?? [];
$profile = $profile ?? null;
$driverTrips = $driverTrips ?? [];
$passengerTrips = $passengerTrips ?? [];
$rating = $rating ?? ['avg' => 0.0, 'count' => 0];
$reviews = $reviews ?? [];
$avatarUrl = is_array($profile) ? (string)($profile['avatar_url'] ?? '') : '';
$pseudo = is_array($profile) ? (string)($profile['pseudo'] ?? '') : '';
?>

<div class="container-xxl px-3 py-4">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h1 class="fw-bold mb-0">PROFILS</h1>
    <a class="btn btn-light border fw-semibold" href="<?= BASE_URL ?>/">Retour accueil</a>
  </div>

  <div class="p-3 p-md-4 rounded-4 bg-white border border-black border-opacity-10 mb-3">
    <form method="get" action="<?= BASE_URL ?>/profils" class="d-flex gap-2 flex-wrap">
      <input
        class="form-control rounded-pill"
        style="max-width:420px;"
        name="q"
        value="<?= htmlspecialchars($q, ENT_QUOTES) ?>"
        placeholder="Rechercher un utilisateur (pseudo)..."
        aria-label="Rechercher un utilisateur"
      >
      <button class="btn btn-ecoride-primary fw-bold rounded-pill" type="submit">Rechercher</button>
    </form>

    <?php if ($q !== '' && empty($searchResults) && !is_array($profile)): ?>
      <p class="text-secondary small mb-0 mt-2">Aucun utilisateur trouvé.</p>
    <?php endif; ?>

    <?php if (!empty($searchResults)): ?>
      <div class="mt-3">
        <div class="text-secondary small mb-2">Résultats :</div>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($searchResults as $u): ?>
            <?php
              $id = (int)($u['id'] ?? 0);
              $p = (string)($u['pseudo'] ?? '');
              $a = (string)($u['avatar_url'] ?? '');
            ?>
            <a href="<?= BASE_URL ?>/profils?userId=<?= $id ?>" class="text-decoration-none">
              <div class="px-3 py-2 rounded-pill border bg-light d-inline-flex align-items-center gap-2">
                <div class="rounded-circle overflow-hidden border bg-white" style="width:28px;height:28px;">
                  <?php if ($a !== ''): ?>
                    <img src="<?= htmlspecialchars($a) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                  <?php else: ?>
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary" style="font-size:10px;">N/A</div>
                  <?php endif; ?>
                </div>
                <span class="fw-semibold text-dark"><?= htmlspecialchars($p, ENT_QUOTES) ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

    <div class="row g-3 align-items-stretch mb-3">

    <div class="col-12 col-lg-6">
        <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100">
        <h2 class="h5 fw-bold mb-3">Profil</h2>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="rounded-circle overflow-hidden border bg-light" style="width:72px;height:72px;">
            <?php if ($avatarUrl !== ''): ?>
                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary small">N/A</div>
            <?php endif; ?>
            </div>

            <div class="flex-grow-1">
            <div class="fw-bold fs-5"><?= htmlspecialchars($pseudo, ENT_QUOTES) ?></div>
            <div class="text-secondary small">
                Note moyenne :
                <strong><?= number_format((float)($rating['avg'] ?? 0), 1, ',', ' ') ?></strong>/5
                (<?= (int)($rating['count'] ?? 0) ?> avis)
            </div>
            </div>
        </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100">
        <h2 class="h5 fw-bold mb-3">Avis reçus</h2>

        <?php if (empty($reviews)): ?>
            <p class="text-secondary mb-0">Aucun avis approuvé.</p>
        <?php else: ?>
            <div class="d-grid gap-3">
            <?php foreach ($reviews as $r): ?>
                <div class="p-3 rounded-4 border bg-light">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div class="fw-semibold"><?= htmlspecialchars((string)($r['author_pseudo'] ?? ''), ENT_QUOTES) ?></div>
                    <div class="text-secondary small">
                    <?= (int)($r['rating'] ?? 0) ?>/5 • <?= htmlspecialchars((string)($r['created_at'] ?? ''), ENT_QUOTES) ?>
                    </div>
                </div>
                <div class="mt-2">
                    <?= nl2br(htmlspecialchars((string)($r['comment'] ?? ''), ENT_QUOTES)) ?>
                </div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>

    </div>

    <div class="row g-3 mb-3">
    <div class="col-12">
        <div class="p-4 rounded-4 bg-white border border-black border-opacity-10">
        <h2 class="h5 fw-bold mb-3">Trajets passés (conducteur)</h2>

        <?php if (empty($driverTrips)): ?>
            <p class="text-secondary mb-0">Aucun trajet terminé en tant que conducteur.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr class="text-secondary small">
                    <th>Trajet</th>
                    <th>Départ</th>
                    <th>Crédits</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($driverTrips as $t): ?>
                    <?php $fromTo = (string)($t['city_from'] ?? '') . ' → ' . (string)($t['city_to'] ?? ''); ?>
                    <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($fromTo, ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($t['departure_datetime'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= (int)($t['price_credits'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        </div>
    </div>
    </div>

    <div class="row g-3">
    <div class="col-12">
        <div class="p-4 rounded-4 bg-white border border-black border-opacity-10">
        <h2 class="h5 fw-bold mb-3">Trajets passés (passager)</h2>

        <?php if (empty($passengerTrips)): ?>
            <p class="text-secondary mb-0">Aucun trajet terminé en tant que passager.</p>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr class="text-secondary small">
                    <th>Trajet</th>
                    <th>Départ</th>
                    <th>Chauffeur</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($passengerTrips as $t): ?>
                    <?php $fromTo = (string)($t['city_from'] ?? '') . ' → ' . (string)($t['city_to'] ?? ''); ?>
                    <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($fromTo, ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($t['departure_datetime'] ?? ''), ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars((string)($t['driver_pseudo'] ?? ''), ENT_QUOTES) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
        </div>
    </div>
    </div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>