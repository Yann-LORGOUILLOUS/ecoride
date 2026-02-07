<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-4 my-lg-5">

  <div class="p-3 p-lg-4 rounded-4 bg-light border text-center mb-4">
    <h1 class="h4 mb-0 fw-bold"><?= htmlspecialchars($pageTitle ?? 'Gestion des signalements techniques') ?></h1>
  </div>

  <?php if (isset($flash['message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info', ENT_QUOTES, 'UTF-8') ?> mb-4">
      <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <?php if (empty($issues)): ?>
    <div class="text-center text-secondary py-5">
      Aucun signalement technique à traiter.
    </div>

    <div class="text-center mt-4">
      <a class="text-decoration-none fw-semibold" href="<?= BASE_URL ?>/dashboard-administrateur">← retour au dashboard</a>
    </div>

  <?php else: ?>

    <?php foreach ($issues as $issue): ?>
      <?php
        $oid = (new IncidentRepository())->getOid($issue);

        $reporterId = (int)($issue['reporter_user_id'] ?? 0);
        $reporterPseudo = (string)($reporters[$reporterId] ?? '');
        $page = (string)($issue['page'] ?? '');
        $subject = (string)($issue['subject'] ?? '');
        $comment = (string)($issue['comment'] ?? '');
        $severity = (string)($issue['severity'] ?? '');
        $createdAt = (string)($issue['created_at'] ?? '');
      ?>

      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-lg-4">

          <div class="row g-3 align-items-stretch text-center">
            <div class="col-12 col-lg-3">
              <div class="p-3 rounded-4 bg-light h-100">
                <div class="fw-bold small text-secondary mb-1">Rédacteur</div>
                <div class="fw-semibold"><?= htmlspecialchars($reporterPseudo !== '' ? $reporterPseudo : ('User #' . $reporterId)) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-2">
              <div class="p-3 rounded-4 bg-light h-100">
                <div class="fw-bold small text-secondary mb-1">Section concernée</div>
                <div class="fw-semibold"><?= htmlspecialchars($page) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-3">
              <div class="p-3 rounded-4 bg-light h-100">
                <div class="fw-bold small text-secondary mb-1">Nature du problème</div>
                <div class="fw-semibold"><?= htmlspecialchars($subject) ?></div>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <div class="p-3 rounded-4 bg-light h-100">
                <div class="fw-bold small text-secondary mb-1">Précisions</div>
                <div><?= nl2br(htmlspecialchars($comment)) ?></div>
              </div>
            </div>
          </div>

          <div class="d-flex flex-column flex-lg-row justify-content-center align-items-center gap-2 gap-lg-3 mt-3">
            <div class="text-secondary small">
              <span class="fw-semibold">Gravité :</span> <?= htmlspecialchars($severity) ?>
              <span class="mx-2">|</span>
              <span class="fw-semibold">Date :</span> <?= htmlspecialchars($createdAt) ?>
            </div>

            <form method="post" action="<?= BASE_URL ?>/gestion-signalements-techniques">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="action" value="validate">
              <input type="hidden" name="incident_oid" value="<?= htmlspecialchars($oid, ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn btn-ecoride-primary fw-bold">
                Valider le signalement
              </button>
            </form>
          </div>

        </div>
      </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
      <a class="text-decoration-none fw-semibold" href="<?= BASE_URL ?>/dashboard-administrateur">← retour au dashboard</a>
    </div>

  <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
