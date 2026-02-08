<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$rating = $rating ?? ['avg' => 0.0, 'count' => 0];
$reviews = $reviews ?? [];

$avg = (float)($rating['avg'] ?? 0);
$count = (int)($rating['count'] ?? 0);
$avgStars = (int)round($avg);

function renderStars(int $value): string {
  $value = max(0, min(5, $value));
  $out = '';
  for ($i = 1; $i <= 5; $i++) {
    $out .= $i <= $value ? '★' : '☆';
  }
  return $out;
}
?>

<div class="container-xxl px-3 py-4">

  <div class="text-center mb-4">
    <div class="text-secondary small">MON COMPTE</div>
    <h1 class="fw-bold mb-0">MES AVIS</h1>
  </div>

  <div class="d-flex justify-content-center mb-4">
    <div class="px-4 py-2 rounded-pill bg-white border border-black border-opacity-10 d-inline-flex align-items-center gap-3">
      <div class="fw-semibold">Ma note :</div>
      <div class="fw-bold text-warning" style="letter-spacing:2px;">
        <?= renderStars($avgStars) ?>
      </div>
      <div class="text-secondary small">
        (<?= number_format($avg, 1, ',', ' ') ?>/5 • <?= $count ?> avis)
      </div>
    </div>
  </div>

  <?php if (empty($reviews)): ?>
    <div class="row justify-content-center">
      <div class="col-12 col-lg-8">
        <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 text-center text-secondary">
          Aucun avis approuvé pour le moment.
        </div>
      </div>
    </div>
  <?php else: ?>

    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="d-grid gap-3">

          <?php foreach ($reviews as $r): ?>
            <?php
              $author = (string)($r['author_pseudo'] ?? '');
              $createdAt = (string)($r['created_at'] ?? '');
              $cityFrom = (string)($r['city_from'] ?? '');
              $cityTo = (string)($r['city_to'] ?? '');
              $dep = (string)($r['departure_datetime'] ?? '');
              $note = (int)($r['rating'] ?? 0);
              $comment = (string)($r['comment'] ?? '');
            ?>

            <div class="p-3 p-md-4 rounded-4 bg-light border border-black border-opacity-10">
              <div class="row g-3 align-items-stretch">

                <div class="col-12 col-md-3">
                  <div class="h-100 p-3 rounded-4 bg-white border border-black border-opacity-10 text-center">
                    <div class="fw-bold text-uppercase small mb-2">Avis déposé par</div>
                    <div class="fw-semibold"><?= htmlspecialchars($author, ENT_QUOTES) ?></div>
                    <div class="text-secondary small mt-2"><?= htmlspecialchars($createdAt, ENT_QUOTES) ?></div>
                  </div>
                </div>

                <div class="col-12 col-md-3">
                  <div class="h-100 p-3 rounded-4 bg-white border border-black border-opacity-10 text-center">
                    <div class="fw-bold text-uppercase small mb-2">Trajet</div>
                    <div class="fw-semibold"><?= htmlspecialchars($dep, ENT_QUOTES) ?></div>
                    <div class="text-secondary small mt-2"><?= htmlspecialchars($cityFrom, ENT_QUOTES) ?></div>
                    <div class="text-secondary small"><?= htmlspecialchars($cityTo, ENT_QUOTES) ?></div>
                  </div>
                </div>

                <div class="col-12 col-md-2">
                  <div class="h-100 p-3 rounded-4 bg-white border border-black border-opacity-10 text-center">
                    <div class="fw-bold text-uppercase small mb-2">Note obtenue</div>
                    <div class="fw-bold text-warning" style="letter-spacing:2px; font-size:18px;">
                      <?= renderStars($note) ?>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="h-100 p-3 rounded-4 bg-white border border-black border-opacity-10">
                    <div class="fw-bold text-uppercase small mb-2 text-center">Commentaire</div>
                    <div><?= nl2br(htmlspecialchars($comment, ENT_QUOTES)) ?></div>
                  </div>
                </div>

              </div>
            </div>

          <?php endforeach; ?>

        </div>
      </div>
    </div>

  <?php endif; ?>

  <div class="text-center mt-4">
    <a class="btn btn-light border fw-semibold rounded-pill px-4" href="<?= BASE_URL ?>/mon-compte">
      ← retour au menu
    </a>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
