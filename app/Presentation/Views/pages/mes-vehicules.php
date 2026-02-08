<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
$vehicules = $vehicules ?? [];
$redirect = $redirect ?? '';
?>

<div class="container-xxl px-3 py-4">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
      <div class="text-secondary small">MON COMPTE</div>
      <h1 class="fw-bold mb-0">MES VÉHICULES</h1>
    </div>
    <a class="btn btn-light border fw-semibold" href="<?= BASE_URL ?>/mon-compte">← Retour sur mon compte</a>
  </div>

  <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
      <?= htmlspecialchars((string)$flash['message']) ?>
    </div>
  <?php endif; ?>

  <div class="row g-3">

    <?php foreach ($vehicules as $v): ?>
      <?php
        $id = (int)($v['id'] ?? 0);
        $plate = (string)($v['license_plate'] ?? '');
        $firstReg = (string)($v['first_registration_date'] ?? '');
        $brand = (string)($v['brand'] ?? '');
        $model = (string)($v['model'] ?? '');
        $color = (string)($v['color'] ?? '');
        $energy = (string)($v['energy_type'] ?? 'electric');
        $seatsAvail = (int)($v['seats_available_default'] ?? 1);
      ?>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100">

          <div class="fw-bold mb-3 text-uppercase text-center">VÉHICULE</div>

          <form method="post" action="<?= BASE_URL ?>/mes-vehicules" class="d-grid gap-2">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="vehicule_id" value="<?= $id ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES) ?>">

            <div>
              <label class="form-label fw-semibold small mb-1">Plaque d’immatriculation</label>
              <input class="form-control rounded-pill" name="license_plate" value="<?= htmlspecialchars($plate, ENT_QUOTES) ?>" required>
            </div>

            <div>
              <label class="form-label fw-semibold small mb-1">Date de première immatriculation</label>
              <input class="form-control rounded-pill" type="date" name="first_registration_date" value="<?= htmlspecialchars($firstReg, ENT_QUOTES) ?>" required>
            </div>

            <div>
              <label class="form-label fw-semibold small mb-1">Marque</label>
              <input class="form-control rounded-pill text-uppercase" name="brand" value="<?= htmlspecialchars($brand, ENT_QUOTES) ?>" required>
            </div>

            <div>
              <label class="form-label fw-semibold small mb-1">Modèle</label>
              <input class="form-control rounded-pill" name="model" value="<?= htmlspecialchars($model, ENT_QUOTES) ?>" required>
            </div>

            <div>
              <label class="form-label fw-semibold small mb-1">Couleur</label>
              <input class="form-control rounded-pill" name="color" value="<?= htmlspecialchars($color, ENT_QUOTES) ?>" required>
            </div>

            <div>
              <label class="form-label fw-semibold small mb-1">Énergie</label>
              <select class="form-select rounded-pill" name="energy_type" required>
                <option value="electric" <?= $energy === 'electric' ? 'selected' : '' ?>>Électrique</option>
                <option value="hybrid" <?= $energy === 'hybrid' ? 'selected' : '' ?>>Hybride</option>
                <option value="fuel" <?= $energy === 'fuel' ? 'selected' : '' ?>>Thermique</option>
              </select>
            </div>

            <div>
              <label class="form-label fw-semibold small mb-1">Nombre de places disponibles</label>
              <input class="form-control rounded-pill" type="number" min="1" max="8" name="seats_available_default" value="<?= $seatsAvail ?>" required>
              <div class="form-text">Places passagers (hors conducteur).</div>
            </div>

            <button class="btn btn-ecoride-primary fw-bold rounded-pill mt-2" type="submit">
              MODIFIER VÉHICULE
            </button>
          </form>

          <form method="post" action="<?= BASE_URL ?>/mes-vehicules" class="mt-2"
                onsubmit="return confirm('Supprimer ce véhicule ?');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="vehicule_id" value="<?= $id ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES) ?>">
            <button class="btn btn-light border fw-semibold rounded-pill w-100" type="submit">
              SUPPRIMER VÉHICULE
            </button>
          </form>

        </div>
      </div>
    <?php endforeach; ?>

    <div class="col-12 col-md-6 col-lg-4">
      <button type="button"
              class="w-100 p-4 rounded-4 bg-white border border-black border-opacity-10 h-100 d-flex flex-column align-items-center justify-content-center"
              data-bs-toggle="modal"
              data-bs-target="#addVehiculeModal"
              style="min-height: 420px;">
        <div class="display-4 fw-bold text-secondary">+</div>
        <div class="fw-bold mt-2">AJOUTER VÉHICULE</div>
      </button>
    </div>

  </div>
</div>

<div class="modal fade" id="addVehiculeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h2 class="h5 fw-bold mb-0">Ajouter un véhicule</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body">
        <form method="post" action="<?= BASE_URL ?>/mes-vehicules" class="d-grid gap-3">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
          <input type="hidden" name="action" value="create">
          <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES) ?>">

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Plaque d’immatriculation</label>
              <input class="form-control rounded-pill" name="license_plate" required placeholder="AA-123-BB">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Date de première immatriculation</label>
              <input class="form-control rounded-pill" type="date" name="first_registration_date" required>
            </div>
          </div>

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Marque</label>
              <input class="form-control rounded-pill" name="brand" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Modèle</label>
              <input class="form-control rounded-pill" name="model" required>
            </div>
          </div>

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Couleur</label>
              <input class="form-control rounded-pill" name="color" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Énergie</label>
              <select class="form-select rounded-pill" name="energy_type" required>
                <option value="electric">Électrique</option>
                <option value="hybrid">Hybride</option>
                <option value="fuel">Thermique</option>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label fw-semibold">Nombre de places disponibles</label>
            <input class="form-control rounded-pill" type="number" min="1" max="8" name="seats_available_default" value="3" required>
            <div class="form-text">Places passagers (hors conducteur).</div>
          </div>

          <div class="d-flex gap-2 justify-content-end pt-2">
            <button type="button" class="btn btn-light border fw-semibold rounded-pill" data-bs-dismiss="modal">
              Annuler
            </button>
            <button class="btn btn-ecoride-primary fw-bold rounded-pill" type="submit">
              Ajouter
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
