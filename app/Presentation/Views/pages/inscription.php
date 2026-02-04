<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
?>

<div class="container-xxl px-3 py-5">
  <div class="d-flex justify-content-center">
    <div class="card border-0 shadow-sm rounded-4 bg-secondary text-white"
         style="max-width: 600px; width: 100%;">
      <div class="card-body p-4 p-md-5">

        <h1 class="text-center fw-bold mb-4" style="letter-spacing: .22em;">
          INSCRIPTION
        </h1>

        <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
          <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
            <?= htmlspecialchars((string)$flash['message']) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/inscription" class="d-grid gap-3" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

          <div class="d-grid gap-2">
            <label for="pseudo" class="text-uppercase text-white-50 fw-semibold small" style="letter-spacing:.12em;">Pseudo</label>
            <input id="pseudo" name="pseudo" type="text" class="form-control rounded-pill py-2 px-3" required>
          </div>

          <div class="row g-3">
            <div class="col-md-6 d-grid gap-2">
              <label for="first_name" class="text-uppercase text-white-50 fw-semibold small" style="letter-spacing:.12em;">Prénom</label>
              <input id="first_name" name="first_name" type="text" class="form-control rounded-pill py-2 px-3" required>
            </div>
            <div class="col-md-6 d-grid gap-2">
              <label for="last_name" class="text-uppercase text-white-50 fw-semibold small" style="letter-spacing:.12em;">Nom</label>
              <input id="last_name" name="last_name" type="text" class="form-control rounded-pill py-2 px-3" required>
            </div>
          </div>

          <div class="d-grid gap-2">
            <label for="email" class="text-uppercase text-white-50 fw-semibold small" style="letter-spacing:.12em;">Email</label>
            <input id="email" name="email" type="email" class="form-control rounded-pill py-2 px-3" autocomplete="email" required>
          </div>

          <div class="row g-3">
            <div class="col-md-6 d-grid gap-2">
              <label for="password" class="text-uppercase text-white-50 fw-semibold small" style="letter-spacing:.12em;">Mot de passe</label>
              <input id="password" name="password" type="password" class="form-control rounded-pill py-2 px-3" autocomplete="new-password" required>
            </div>
            <div class="col-md-6 d-grid gap-2">
              <label for="password_confirm" class="text-uppercase text-white-50 fw-semibold small" style="letter-spacing:.12em;">Confirmation</label>
              <input id="password_confirm" name="password_confirm" type="password" class="form-control rounded-pill py-2 px-3" autocomplete="new-password" required>
            </div>
          </div>

          <button type="submit" class="btn btn-outline-light rounded-pill fw-bold py-2 mt-2">
            CRÉER MON COMPTE
          </button>

          <div class="text-center mt-2 fst-italic text-white-50">
            Déjà un compte ?
            <a href="<?= BASE_URL ?>/connexion" class="link-light">Connectez-vous</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
