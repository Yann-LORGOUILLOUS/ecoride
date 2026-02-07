<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
function sortLink(string $label, string $col, string $currentSort, string $currentDir, string $q): string {
    $dir = ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc';
    $icon = '';

    if ($currentSort === $col) {
        $icon = $currentDir === 'asc' ? ' ▲' : ' ▼';
    }

    $url = BASE_URL . '/gestion-comptes-utilisateurs'
        . '?sort=' . urlencode($col)
        . '&dir=' . urlencode($dir)
        . '&q=' . urlencode($q);

    return '<a class="text-decoration-none text-dark fw-semibold" href="' . $url . '">' . $label . $icon . '</a>';
}
?>

<div class="container-xxl py-4">

  <?php if (isset($flash['message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info', ENT_QUOTES, 'UTF-8') ?> mb-4">
      <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
    <div>
      <h1 class="h4 mb-1">Gestion des comptes</h1>
      <div class="text-secondary small">Liste des utilisateurs</div>
    </div>

    <div class="d-flex flex-column flex-sm-row gap-2">
      <form method="get" action="<?= BASE_URL ?>/gestion-comptes-utilisateurs" class="d-flex gap-2">
        <input
          class="form-control"
          type="search"
          name="q"
          placeholder="Rechercher un compte (pseudo/email)"
          value="<?= htmlspecialchars((string)($q ?? ''), ENT_QUOTES, 'UTF-8') ?>"
        />
        <input type="hidden" name="sort" value="<?= htmlspecialchars((string)($sort ?? 'pseudo'), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="dir" value="<?= htmlspecialchars((string)($dir ?? 'asc'), ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
      </form>

      <button class="btn btn-ecoride-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createUserModal">
        Créer un compte
      </button>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr class="small text-secondary text-center align-middle">
                <th><?= sortLink('Nom', 'pseudo', $sort, $dir, $q) ?></th>
                <th><?= sortLink('Date de création', 'created_at', $sort, $dir, $q) ?></th>
                <th><?= sortLink('Rôle', 'role', $sort, $dir, $q) ?></th>
                <th><?= sortLink('Statut', 'suspended', $sort, $dir, $q) ?></th>
                <th>Signalements confirmés</th>
                <th>Actions administrateur</th>
            </tr>
            </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="6" class="text-center text-secondary py-4">Aucun compte trouvé.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <?php
                  $isSuspended = ((int)($u['suspended'] ?? 0)) === 1;
                  $role = (string)($u['role'] ?? '');
                  $roleLabel = $role === 'admin' ? 'ADMIN' : ($role === 'employee' ? 'MODÉRATEUR' : 'UTILISATEUR');
                ?>
                <tr class="text-center align-middle">
                  <td class="fw-semibold">
                    <?= htmlspecialchars((string)($u['pseudo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    <div class="small text-secondary"><?= htmlspecialchars((string)($u['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td><?= htmlspecialchars((string)($u['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php if ($isSuspended): ?>
                      <span class="badge text-bg-danger">SUSPENDU</span>
                    <?php else: ?>
                      <span class="badge text-bg-success">ACTIF</span>
                    <?php endif; ?>
                  </td>
                  <td><?= (int)($u['validated_reports_count'] ?? 0) ?></td>
                  <td class="text-center">
                    <?php if ($role !== 'admin'): ?>
                      <form method="post" action="<?= BASE_URL ?>/gestion-comptes-utilisateurs" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="action" value="toggle_suspend">
                        <input type="hidden" name="user_id" value="<?= (int)($u['id'] ?? 0) ?>">
                        <input type="hidden" name="next_suspended" value="<?= $isSuspended ? 0 : 1 ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                          <?= $isSuspended ? 'Réactiver' : 'Suspendre' ?>
                        </button>
                      </form>
                    <?php else: ?>
                      <span class="text-secondary small">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="text-center mt-4">
    <a class="text-decoration-none fw-semibold"
        href="<?= BASE_URL ?>/dashboard-administrateur">
        ← retour au dashboard
    </a>
</div>

</div>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="<?= BASE_URL ?>/gestion-comptes-utilisateurs">
        <div class="modal-header">
          <h2 class="modal-title h5">Créer un compte</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="action" value="create_user">

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label">Pseudo</label>
              <input class="form-control" name="pseudo" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Rôle</label>
              <select class="form-select" name="role" required>
                <option value="user">Utilisateur</option>
                <option value="employee">Modérateur</option>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Nom</label>
              <input class="form-control" name="last_name" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Prénom</label>
              <input class="form-control" name="first_name" required>
            </div>

            <div class="col-12">
              <label class="form-label">Email</label>
              <input class="form-control" name="email" type="email" required>
            </div>

            <div class="col-12">
              <label class="form-label">Mot de passe</label>
              <input class="form-control" name="password" type="password" minlength="8" required>
              <div class="form-text">Minimum 8 caractères.</div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-ecoride-primary fw-bold">Créer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
