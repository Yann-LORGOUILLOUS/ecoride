<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
$trips = $trips ?? [];
$old = $old ?? [];
?>

<div class="container-xxl px-3 py-5">
  <div class="d-flex justify-content-center">
    <div class="card border-0 shadow-sm rounded-4 bg-body" style="max-width: 760px; width: 100%;">
      <div class="card-body p-4 p-md-5">

        <h1 class="text-center fw-bold mb-2">Rédiger un avis</h1>
        <p class="text-center text-secondary mb-4">Les avis sont publiés après validation par la modération.</p>

        <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
          <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
            <?= htmlspecialchars((string)$flash['message']) ?>
          </div>
        <?php endif; ?>

        <?php if (!is_array($trips) || count($trips) === 0): ?>
          <div class="alert alert-info rounded-4 mb-0">
            Aucun trajet terminé éligible pour déposer un avis (ou vous avez déjà tout noté).
          </div>
        <?php else: ?>
          <form method="post" action="<?= BASE_URL ?>/rediger-avis" class="d-grid gap-3" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)$csrfToken) ?>">

            <div class="d-grid gap-2">
              <label for="trip_id" class="text-uppercase text-secondary fw-semibold small" style="letter-spacing:.12em;">Trajet</label>
              <select id="trip_id" name="trip_id" class="form-select rounded-pill py-2 px-3" required>
                <option value="">Choisir un trajet terminé</option>
                <?php foreach ($trips as $t): ?>
                  <?php
                    $id = (int)($t['trip_id'] ?? 0);
                    $from = (string)($t['city_from'] ?? '');
                    $to = (string)($t['city_to'] ?? '');
                    $date = (string)($t['departure_datetime'] ?? '');
                    $driver = (string)($t['driver_pseudo'] ?? '');
                    $label = trim($from . ' → ' . $to) . ($date !== '' ? ' — ' . $date : '') . ($driver !== '' ? ' — ' . $driver : '');
                    $selected = ((string)($old['trip_id'] ?? '') === (string)$id) ? 'selected' : '';
                  ?>
                  <option value="<?= $id ?>" <?= $selected ?>><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="d-grid gap-2">
              <label for="rating" class="text-uppercase text-secondary fw-semibold small" style="letter-spacing:.12em;">Note</label>
              <select id="rating" name="rating" class="form-select rounded-pill py-2 px-3" required>
                <option value="">Choisir une note</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                  <?php $selected = ((string)($old['rating'] ?? '') === (string)$i) ? 'selected' : ''; ?>
                  <option value="<?= $i ?>" <?= $selected ?>><?= $i ?>/5</option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="d-grid gap-2">
              <label for="comment" class="text-uppercase text-secondary fw-semibold small" style="letter-spacing:.12em;">Commentaire</label>
              <textarea id="comment" name="comment" class="form-control rounded-4 p-3" rows="5" required><?= htmlspecialchars((string)($old['comment'] ?? '')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-ecoride-primary btn-lg rounded-pill fw-bold">
              Envoyer l’avis
            </button>
          </form>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
