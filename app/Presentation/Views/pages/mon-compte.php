<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
// Sécurité minimale
$dashboardMode = $dashboardMode ?? 'both';

$isPassenger = $dashboardMode === 'passenger' || $dashboardMode === 'both';
$isDriver    = $dashboardMode === 'driver'    || $dashboardMode === 'both';

$userId = (int) ($_SESSION['user']['id'] ?? 0);
?>

<section class="py-4" style="background: var(--ecoride-background);">
  <div class="container-xxl px-3">

    <div class="text-center mb-4">
      <h1 class="fw-bold mb-2">MON COMPTE</h1>
      <a
        href="<?= BASE_URL ?>/profils?userId=<?= $userId ?>"
        class="btn btn-ecoride-primary fw-bold rounded-pill"
      >
        Voir mon profil public
      </a>
    </div>

    <div class="d-flex justify-content-center gap-2 flex-wrap mb-4">
      <form method="post">
        <input type="hidden" name="dashboard_mode" value="passenger">
        <button
          class="btn rounded-pill fw-bold
          <?= $dashboardMode === 'passenger' ? 'btn-ecoride-primary' : 'btn-light border' ?>"
        >
          Passager
        </button>
      </form>

      <form method="post">
        <input type="hidden" name="dashboard_mode" value="driver">
        <button
          class="btn rounded-pill fw-bold
          <?= $dashboardMode === 'driver' ? 'btn-ecoride-primary' : 'btn-light border' ?>"
        >
          Chauffeur
        </button>
      </form>

      <form method="post">
        <input type="hidden" name="dashboard_mode" value="both">
        <button
          class="btn rounded-pill fw-bold
          <?= $dashboardMode === 'both' ? 'btn-ecoride-primary' : 'btn-light border' ?>"
        >
          Les deux
        </button>
      </form>
    </div>

    <div class="row g-3 justify-content-center">

      <div class="col-12 col-lg-4">
        <div class="p-3 rounded-4 bg-white border border-black border-opacity-10 h-100">
          <h2 class="h5 fw-bold text-center mb-3">Mes informations</h2>

          <div class="text-center text-secondary small">
            <div><?= htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES) ?></div>
            <div><?= htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES) ?></div>
            <div><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?></div>
          </div>

          <div class="d-grid gap-2 mt-3">
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-informations">
              Modifier mes infos
            </a>
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-informations">
              Modifier mon mot de passe
            </a>
          </div>
        </div>
      </div>

      <?php if ($isDriver): ?>
      <div class="col-12 col-lg-4">
        <div class="p-3 rounded-4 bg-white border border-black border-opacity-10 h-100">
          <h2 class="h5 fw-bold text-center mb-3">Mes véhicules</h2>

          <div class="text-center text-secondary small">
            <?php if (!empty($vehiculesPreview)): ?>
              <?php foreach ($vehiculesPreview as $v): ?>
                <div>
                  <?= htmlspecialchars($v['brand'] ?? '', ENT_QUOTES) ?>
                  <?= htmlspecialchars($v['model'] ?? '', ENT_QUOTES) ?>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div>-</div>
            <?php endif; ?>
          </div>

          <div class="d-grid gap-2 mt-3">
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-vehicules">
              Ajouter / Supprimer
            </a>
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-vehicules">
              Modifier mes véhicules
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="col-12 col-lg-4">
        <div class="p-3 rounded-4 bg-white border border-black border-opacity-10 h-100">
          <h2 class="h5 fw-bold text-center mb-3">Mes crédits</h2>

          <div class="text-center text-secondary small">
            Solde crédit :
            <strong><?= (int) ($user['credits'] ?? 0) ?></strong> crédits
          </div>

          <div class="d-grid gap-2 mt-3">
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-credits">
              À quoi servent mes crédits ?
            </a>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="p-3 rounded-4 bg-white border border-black border-opacity-10 h-100">
          <h2 class="h5 fw-bold text-center mb-3">Mes actions rapides</h2>

          <div class="d-grid gap-2">

            <?php if ($isDriver): ?>
              <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/creer-trajet">
                Proposer un trajet
              </a>
              <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-trajets">
                Mes trajets actifs
              </a>
            <?php endif; ?>

            <?php if ($isPassenger): ?>
              <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/trajets">
                Rechercher un trajet
              </a>
              <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-reservations">
                Mes réservations
              </a>
            <?php endif; ?>

          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="p-3 rounded-4 bg-white border border-black border-opacity-10 h-100">
          <h2 class="h5 fw-bold text-center mb-3">Mes avis</h2>

          <?php
            $avg   = (float) ($rating['avg'] ?? 0);
            $count = (int)   ($rating['count'] ?? 0);
          ?>

          <div class="text-center text-secondary small mb-2">
            Ma note :
            <strong><?= number_format($avg, 1, ',', ' ') ?></strong> / 5
            (<?= $count ?>)
          </div>

          <div class="d-grid gap-2">
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/mes-avis">
              Voir mes avis
            </a>
            <a class="btn btn-light border rounded-pill fw-semibold" href="<?= BASE_URL ?>/rediger-avis">
              Déposer un avis
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
