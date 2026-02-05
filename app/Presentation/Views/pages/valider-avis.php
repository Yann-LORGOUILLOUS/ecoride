<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$pendingReviews = $pendingReviews ?? [];
$pendingReviewsCount = (int)($pendingReviewsCount ?? 0);
$flash = $flash ?? null;
$csrfToken = (string)($csrfToken ?? '');

$stars = function (int $rating): string {
    $rating = max(0, min(5, $rating));
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $rating ? '★' : '☆';
    }
    return $out;
};
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

  <h2 class="h6 fw-bold text-center mb-4">
    VALIDER LES AVIS (<?= $pendingReviewsCount ?>)
  </h2>

  <?php if (empty($pendingReviews)): ?>
    <div class="alert alert-secondary rounded-4 mb-0">
      Aucun avis en attente pour le moment.
    </div>
  <?php else: ?>

    <div class="d-flex flex-column gap-4">
      <?php foreach ($pendingReviews as $r): ?>
        <?php
          $reviewId = (int)($r['id'] ?? 0);
          $tripId = (int)($r['trip_id'] ?? 0);
          $authorPseudo = (string)($r['author_pseudo'] ?? '');
          $authorRole = (string)($r['author_role'] ?? '');
          $recipientPseudo = (string)($r['recipient_pseudo'] ?? '');
          $recipientRole = (string)($r['recipient_role'] ?? '');
          $rating = (int)($r['rating'] ?? 0);
          $comment = (string)($r['comment'] ?? '');
          $modalId = 'rejectReviewModal_' . $reviewId;
        ?>

        <div class="bg-light border rounded-4 p-3 p-lg-4">
          <div class="row g-3 align-items-stretch">

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-4 p-3 h-100 text-center">
                <div class="fw-bold">REDACTEUR :</div>
                <div class="fw-semibold"><?= htmlspecialchars($authorPseudo) ?></div>
                <div class="text-secondary"><?= htmlspecialchars($authorRole) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="bg-white rounded-4 p-3 h-100 text-center">
                <div class="fw-bold">DESTINATAIRE :</div>
                <div class="fw-semibold"><?= htmlspecialchars($recipientPseudo) ?></div>
                <div class="text-secondary"><?= htmlspecialchars($recipientRole) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-2">
              <div class="bg-white rounded-4 p-3 h-100 text-center">
                <div class="fw-bold">NOTE ATTRIBUÉE :</div>
                <div class="fs-4" aria-label="Note <?= $rating ?> sur 5"><?= htmlspecialchars($stars($rating)) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <div class="bg-white rounded-4 p-3 h-100">
                <div class="fw-bold text-center mb-2">COMMENTAIRE :</div>
                <div class="text-secondary"><?= nl2br(htmlspecialchars($comment)) ?></div>
              </div>
            </div>

          </div>

          <div class="d-flex flex-column flex-lg-row justify-content-center gap-2 gap-lg-3 mt-3">

            <a class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"
               href="<?= BASE_URL ?>/details-trajet?id=<?= $tripId ?>">
              DETAILS DU TRAJET
            </a>

            <form method="post" action="<?= BASE_URL ?>/valider-avis" class="m-0">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="review_id" value="<?= $reviewId ?>">
              <button class="btn btn-ecoride-primary rounded-pill px-4 fw-bold" type="submit">
                VALIDER CET AVIS
              </button>
            </form>

            <button type="button"
                    class="btn btn-outline-danger rounded-pill px-4 fw-bold"
                    data-bs-toggle="modal"
                    data-bs-target="#<?= htmlspecialchars($modalId) ?>">
              REJETER CET AVIS
            </button>

          </div>
        </div>

        <div class="modal fade" id="<?= htmlspecialchars($modalId) ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
              <div class="modal-header">
                <h5 class="modal-title fw-bold">Rejeter cet avis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
              </div>

              <form method="post" action="<?= BASE_URL ?>/valider-avis">
                <div class="modal-body">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="action" value="reject">
                  <input type="hidden" name="review_id" value="<?= $reviewId ?>">

                  <label class="form-label fw-semibold" for="reason_<?= $reviewId ?>">Pour quelles raisons :</label>
                  <textarea id="reason_<?= $reviewId ?>"
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
