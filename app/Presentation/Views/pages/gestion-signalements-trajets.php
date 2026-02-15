<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$items = $items ?? [];
$flash = $flash ?? null;
$csrfToken = (string)($csrfToken ?? '');
?>

<div class="container my-4 my-lg-5">

  <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
      <?= htmlspecialchars((string)$flash['message']) ?>
    </div>
  <?php endif; ?>

  <div class="p-3 p-lg-4 rounded-4 bg-light border text-center mb-3">
    <h1 class="h3 mb-0 fw-bold">DASHBOARD MODERATEUR</h1>
  </div>

  <h2 class="h6 fw-bold text-center mb-4">GESTION DES SIGNALEMENTS</h2>

  <?php if (empty($items)): ?>
    <div class="alert alert-secondary rounded-4 mb-0">Aucun signalement en attente.</div>
  <?php else: ?>

    <div class="d-flex flex-column gap-4">
      <?php foreach ($items as $it): ?>
        <?php
          $oid = (string)($it['oid'] ?? '');
          $trip = $it['trip'] ?? null;
          $reporter = $it['reporter'] ?? null;
          $target = $it['target'] ?? null;
          $comment = (string)($it['comment'] ?? '');
          $rejectModalId = 'rejectIncident_' . $oid;
        ?>

        <div class="bg-light border rounded-4 p-3 p-lg-4">
          <div class="row g-3 align-items-stretch">

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-4 p-3 h-100 text-center">
                <div class="fw-bold">TRAJET :</div>
                <div class="text-secondary">
                  <?= is_array($trip) ? htmlspecialchars((string)($trip['city_from'] ?? '')) : '—' ?>
                  →
                  <?= is_array($trip) ? htmlspecialchars((string)($trip['city_to'] ?? '')) : '—' ?>
                </div>
                <div class="text-secondary small">
                  <span class="fw-semibold">Départ :</span>
                  <?= is_array($trip) ? htmlspecialchars((string)($trip['departure_datetime'] ?? '—')) : '—' ?>
                </div>
                <div class="text-secondary small">
                  <span class="fw-semibold">Arrivée :</span>
                  <?= is_array($trip) ? htmlspecialchars((string)($trip['arrival_datetime'] ?? '—')) : '—' ?>
                </div>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-4 p-3 h-100 text-center">
                <div class="fw-bold">REDACTEUR :</div>
                <div class="fw-semibold">
                  <?= is_array($reporter) ? htmlspecialchars((string)($reporter['pseudo'] ?? '')) : '—' ?>
                </div>
                <div class="text-secondary small">
                  <?= is_array($reporter) ? htmlspecialchars((string)($reporter['email'] ?? '—')) : '—' ?>
                </div>
                <div class="text-secondary"><?= htmlspecialchars((string)($it['reporter_trip_role'] ?? '')) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-4 p-3 h-100 text-center">
                <div class="fw-bold">DESTINATAIRE :</div>
                <div class="fw-semibold">
                  <?= is_array($target) ? htmlspecialchars((string)($target['pseudo'] ?? '')) : '—' ?>
                </div>
                <div class="text-secondary small">
                  <?= is_array($target) ? htmlspecialchars((string)($target['email'] ?? '')) : '—' ?>
                </div>
                <div class="text-secondary"><?= htmlspecialchars((string)($it['target_trip_role'] ?? '')) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-4 p-3 h-100">
                <div class="fw-bold text-center mb-2">OBJET DU SIGNALEMENT :</div>
                <div class="text-secondary"><?= nl2br(htmlspecialchars($comment)) ?></div>
              </div>
            </div>
          </div>

          <div class="d-flex flex-column flex-lg-row justify-content-center gap-2 gap-lg-3 mt-3">

            <form method="post" action="<?= BASE_URL ?>/gestion-signalements-trajets" class="m-0">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="incident_oid" value="<?= htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') ?>">
              <button class="btn btn-ecoride-primary rounded-pill px-4 fw-bold" type="submit">
                VALIDER LE SIGNALEMENT
              </button>
            </form>

            <button type="button"
                    class="btn btn-outline-danger rounded-pill px-4 fw-bold"
                    data-bs-toggle="modal"
                    data-bs-target="#<?= htmlspecialchars($rejectModalId, ENT_QUOTES, 'UTF-8') ?>">
              REJETER LE SIGNALEMENT
            </button>

            <form method="post" action="<?= BASE_URL ?>/gestion-signalements-trajets" class="m-0">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="action" value="escalate">
              <input type="hidden" name="incident_oid" value="<?= htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') ?>">
              <button class="btn btn-outline-secondary rounded-pill px-4 fw-bold" type="submit">
                TRANSFERT VERS ADMINISTRATEUR
              </button>
            </form>

          </div>
        </div>

        <div class="modal fade" id="<?= htmlspecialchars($rejectModalId, ENT_QUOTES, 'UTF-8') ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
              <div class="modal-header">
                <h5 class="modal-title fw-bold">Rejeter le signalement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
              </div>

              <form method="post" action="<?= BASE_URL ?>/gestion-signalements-trajets">
                <div class="modal-body">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="action" value="reject">
                  <input type="hidden" name="incident_oid" value="<?= htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') ?>">

                  <label class="form-label fw-semibold" for="reason_<?= htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') ?>">Pour quelles raisons :</label>
                  <textarea id="reason_<?= htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') ?>"
                            name="reject_reason"
                            class="form-control"
                            rows="4"
                            required
                            maxlength="1000"></textarea>

                  <div class="form-text text-secondary">
                    Cette raison sera envoyée par email au rédacteur.
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Annuler</button>
                  <button type="submit" class="btn btn-danger rounded-pill fw-bold">Confirmer le rejet</button>
                </div>
              </form>

            </div>
          </div>
        </div>

      <?php endforeach; ?>
    </div>

  <?php endif; ?>

  <div class="text-center mt-4">
    <a class="text-secondary text-decoration-none fw-semibold" href="<?= BASE_URL ?>/dashboard-moderateur">
      &larr; retour au dashboard
    </a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
