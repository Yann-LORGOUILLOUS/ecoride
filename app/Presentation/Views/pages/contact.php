<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$flash = $flash ?? null;
$csrfToken = $csrfToken ?? '';
?>

<div class="container-xxl px-3 py-4">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <h1 class="fw-bold mb-0">CONTACT</h1>
    <a class="btn btn-light border fw-semibold" href="<?= BASE_URL ?>/">Retour accueil</a>
  </div>

  <?php if (is_array($flash) && ($flash['message'] ?? '') !== ''): ?>
    <div class="alert alert-<?= htmlspecialchars((string)($flash['type'] ?? 'info')) ?> rounded-4" role="alert">
      <?= htmlspecialchars((string)$flash['message']) ?>
    </div>
  <?php endif; ?>

  <div class="row g-3 align-items-stretch">

    <div class="col-12 col-lg-5">
      <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100">
        <h2 class="h5 fw-bold mb-3">Coordonnées</h2>

        <div class="text-secondary" style="line-height:1.7;">
          <div class="fw-semibold text-dark">EcoRide</div>
          <div>12 rue du développement durable</div>
          <div>77680 Roissy-en-Brie</div>

          <hr class="my-3">

          <div class="fw-semibold text-dark">Horaires</div>
          <div>Lun–Ven : 9h–18h</div>

          <div class="small mt-3">
            Vos données sont utilisées uniquement pour répondre à votre demande.
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-7">
      <div class="p-4 rounded-4 bg-white border border-black border-opacity-10 h-100">
        <h2 class="h5 fw-bold mb-3">Formulaire</h2>

        <form method="post" action="<?= BASE_URL ?>/contact" class="d-grid gap-3">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Nom</label>
              <input class="form-control rounded-4" name="name" required>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label fw-semibold">Email</label>
              <input class="form-control rounded-4" type="email" name="email" required>
            </div>
          </div>

          <div>
            <label class="form-label fw-semibold">Sujet</label>
            <input class="form-control rounded-4" name="subject" required>
          </div>

          <div>
            <label class="form-label fw-semibold">Message</label>
            <textarea class="form-control rounded-4" name="message" rows="6" required></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button class="btn btn-ecoride-primary fw-bold rounded-pill px-4" type="submit">
              Envoyer
            </button>
          </div>

        </form>

      </div>
    </div>

  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
