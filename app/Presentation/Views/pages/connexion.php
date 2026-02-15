<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
?>

<div class="container-xxl px-3 py-5">
  <div class="d-flex justify-content-center">
    <div class="card border-0 shadow-sm rounded-4 bg-secondary text-white"
         style="max-width: 520px; width: 100%;">
      <div class="card-body p-4 p-md-5">

        <h1 class="text-center fw-bold mb-4" style="letter-spacing: .22em;">
          CONNEXION
        </h1>

        <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
          <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
            <?= htmlspecialchars((string)$flash['message']) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/connexion" class="d-grid gap-3" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

          <div class="d-grid gap-2">
            <label for="email" class="text-uppercase text-white fw-semibold small"
                   style="letter-spacing: .12em;">Email</label>
            <input
              id="email"
              name="email"
              type="email"
              class="form-control rounded-pill py-2 px-3"
              autocomplete="email"
              required
            >
          </div>

          <div class="d-grid gap-2">
            <label for="password" class="text-uppercase text-white fw-semibold small"
                   style="letter-spacing: .12em;">Mot de passe</label>
            <input
              id="password"
              name="password"
              type="password"
              class="form-control rounded-pill py-2 px-3"
              autocomplete="current-password"
              required
            >
          </div>

          <button class="btn btn-outline-light rounded-pill fw-bold py-2 mt-3">
            SE CONNECTER
          </button>

          <div class="text-center mt-2">
            <div class="fst-italic text-white">
              Vous n’avez pas de compte ? <a href="<?= BASE_URL ?>/inscription" class="link-light">Inscrivez-vous</a>
            </div>
            <div class="fst-italic text-white">
              Mot de passe oublié ? <a href="<?= BASE_URL ?>/contact" class="link-light">Cliquez ici</a>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
