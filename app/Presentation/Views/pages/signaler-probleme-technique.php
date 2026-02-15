<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-4 my-lg-5">

  <div class="p-3 p-lg-4 rounded-4 bg-light border text-center mb-4">
    <h1 class="h4 mb-0 fw-bold"><?= htmlspecialchars($pageTitle ?? 'Signaler un Problème Technique') ?></h1>
  </div>

  <?php if (isset($flash['message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info', ENT_QUOTES, 'UTF-8') ?> mb-4">
      <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-lg-4">

      <form method="post" action="<?= BASE_URL ?>/signaler-probleme-technique" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($csrfToken ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <div class="col-12 col-lg-6">
          <label class="form-label fw-semibold" for="page">Section concernée *</label>
          <select class="form-select" id="page" name="page" required>
            <option value="">— Choisir —</option>
            <option value="Accueil">Accueil</option>
            <option value="Covoiturages">Covoiturages</option>
            <option value="Détail trajet">Détail trajet</option>
            <option value="Connexion / Inscription">Connexion / Inscription</option>
            <option value="Mon compte">Mon compte</option>
            <option value="Autre">Autre</option>
          </select>
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label fw-semibold" for="severity">Gravité *</label>
          <select class="form-select" id="severity" name="severity" required>
            <option value="">— Choisir —</option>
            <option value="Faible">Faible</option>
            <option value="Moyenne">Moyenne</option>
            <option value="Élevée">Élevée</option>
            <option value="Critique">Critique</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold" for="subject">Nature du problème *</label>
          <input class="form-control" id="subject" name="subject" type="text" required maxlength="120"
                 placeholder="Ex: Bouton 'Réserver' ne répond pas / Page blanche / ..." />
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold" for="comment">Précisions *</label>
          <textarea class="form-control" id="comment" name="comment" rows="6" required maxlength="2000"
                    placeholder="Description du problème rencontré."></textarea>
        </div>

        <div class="col-12 d-flex justify-content-center">
          <button type="submit" class="btn btn-ecoride-primary fw-bold">
            Envoyer le signalement
          </button>
        </div>
      </form>

    </div>
  </div>

  <div class="text-center mt-4">
    <a class="text-decoration-none fw-semibold" href="<?= BASE_URL ?>/">← retour accueil</a>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
