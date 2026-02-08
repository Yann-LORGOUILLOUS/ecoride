<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
$messages = $messages ?? [];

function statusBadge(string $status): string {
  return match ($status) {
    'new' => 'bg-warning-subtle text-warning-emphasis',
    'read' => 'bg-success-subtle text-success-emphasis',
    'archived' => 'bg-secondary-subtle text-secondary-emphasis',
    default => 'bg-light text-dark',
  };
}
?>

<div class="container my-4 my-lg-5">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <h1 class="h3 mb-0 fw-bold"><?= htmlspecialchars($pageTitle ?? 'Messages de contact') ?></h1>
    <a class="btn btn-light border fw-semibold" href="<?= BASE_URL ?>/dashboard-administrateur">← Retour dashboard</a>
  </div>

  <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
      <?= htmlspecialchars((string)$flash['message']) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($messages)): ?>
    <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 text-secondary text-center">
      Aucun message pour le moment.
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr class="text-secondary small">
            <th>Statut</th>
            <th>Reçu le</th>
            <th>Expéditeur</th>
            <th>Sujet</th>
            <th>Message</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $m): ?>
            <?php
              $id = (int)($m['id'] ?? 0);
              $status = (string)($m['status'] ?? 'new');
              $createdAt = (string)($m['created_at'] ?? '');
              $name = (string)($m['name'] ?? '');
              $email = (string)($m['email'] ?? '');
              $subject = (string)($m['subject'] ?? '');
              $message = (string)($m['message'] ?? '');
              $excerpt = mb_strlen($message) > 140 ? mb_substr($message, 0, 140) . '…' : $message;
            ?>
            <tr>
              <td>
                <span class="badge rounded-pill <?= statusBadge($status) ?>">
                  <?= htmlspecialchars($status, ENT_QUOTES) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($createdAt, ENT_QUOTES) ?></td>
              <td>
                <div class="fw-semibold"><?= htmlspecialchars($name, ENT_QUOTES) ?></div>
                <div class="text-secondary small"><?= htmlspecialchars($email, ENT_QUOTES) ?></div>
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($subject, ENT_QUOTES) ?></td>
              <td>
                <details>
                  <summary class="text-secondary" style="cursor:pointer;">
                    <?= htmlspecialchars($excerpt, ENT_QUOTES) ?>
                  </summary>
                  <div class="mt-2">
                    <?= nl2br(htmlspecialchars($message, ENT_QUOTES)) ?>
                  </div>
                </details>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <form method="post" action="<?= BASE_URL ?>/gestion-messages-contact">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                    <input type="hidden" name="action" value="mark_read">
                    <input type="hidden" name="message_id" value="<?= $id ?>">
                    <button class="btn btn-sm btn-light border fw-semibold" type="submit">Marquer lu</button>
                  </form>

                  <form method="post" action="<?= BASE_URL ?>/gestion-messages-contact" onsubmit="return confirm('Archiver ce message ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                    <input type="hidden" name="action" value="archive">
                    <input type="hidden" name="message_id" value="<?= $id ?>">
                    <button class="btn btn-sm btn-light border fw-semibold" type="submit">Archiver</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
