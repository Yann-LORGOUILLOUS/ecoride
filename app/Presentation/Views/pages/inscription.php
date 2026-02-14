<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
?>

<div class="container-xxl px-3 py-5">
  <div class="d-flex justify-content-center">
    <div class="card border-0 shadow-sm rounded-4 bg-secondary text-white" style="max-width: 600px; width: 100%;">
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
            <div class="col-12">
              <div class="password-help" id="passwordHelp">
                <div class="password-meter" aria-hidden="true">
                  <div id="passwordMeterBar" style="width:0%"></div>
                </div>
                <ul id="passwordRules">
                  <li data-rule="len">12 caractères minimum</li>
                  <li data-rule="low">1 minuscule</li>
                  <li data-rule="up">1 majuscule</li>
                  <li data-rule="dig">1 chiffre</li>
                  <li data-rule="spe">1 caractère spécial</li>
                </ul>
                <small id="passwordFeedback"></small>
              </div>
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

<style>
  .password-meter { height: 10px; border-radius: 999px; background: rgba(255,255,255,.15); overflow: hidden; }
  #passwordMeterBar { height: 100%; border-radius: 999px; background: var(--ecoride-primary); transition: width .2s ease; }
  #passwordRules { margin: .75rem 0 0; padding-left: 1.1rem; }
  #passwordRules li { line-height: 1.35; }
  #passwordFeedback { display: inline-block; margin-top: .35rem; opacity: .85; }
</style>

<script>
(function () {
  const input = document.querySelector('input[name="password"]');
  const confirm = document.querySelector('input[name="password_confirm"]');
  const submitBtn = document.querySelector('button[type="submit"]');
  if (!input) return;

  const bar = document.getElementById('passwordMeterBar');
  const feedback = document.getElementById('passwordFeedback');
  const rules = document.getElementById('passwordRules');

  const tests = {
    len: (v) => v.length >= 12,
    low: (v) => /[a-z]/.test(v),
    up:  (v) => /[A-Z]/.test(v),
    dig: (v) => /\d/.test(v),
    spe: (v) => /[^a-zA-Z0-9]/.test(v),
  };

  function score(v) {
    let ok = 0;
    for (const k in tests) ok += tests[k](v) ? 1 : 0;
    return ok;
  }

  function sync() {
    const v = input.value || '';
    const s = score(v);
    const pct = (s / 5) * 100;

    if (bar) bar.style.width = pct + '%';

    if (rules) {
      const items = rules.querySelectorAll('li[data-rule]');
      items.forEach(li => {
        const k = li.getAttribute('data-rule');
        li.style.opacity = tests[k](v) ? '1' : '0.45';
        li.style.textDecoration = tests[k](v) ? 'none' : 'line-through';
      });
    }

    if (feedback) {
      if (v.length === 0) feedback.textContent = '';
      else if (s <= 2) feedback.textContent = 'Faible';
      else if (s === 3) feedback.textContent = 'Correct';
      else if (s === 4) feedback.textContent = 'Bon';
      else feedback.textContent = 'Très bon';
    }

    const confirmOk = confirm ? (confirm.value === v && v.length > 0) : true;
    const ok = (s === 5) && confirmOk;
    if (submitBtn) submitBtn.disabled = !ok;
  }

  input.addEventListener('input', sync);
  if (confirm) confirm.addEventListener('input', sync);
  sync();
})();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
