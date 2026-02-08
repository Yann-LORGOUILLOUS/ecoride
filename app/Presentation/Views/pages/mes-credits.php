<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$credits = isset($credits) ? (int)$credits : 0;
?>

<div class="container-xxl px-3 py-4">

  <div class="text-center mb-4">
    <div class="text-secondary small">MON COMPTE</div>
    <h1 class="fw-bold mb-0">MES CRÉDITS</h1>
  </div>

  <div class="d-flex justify-content-center mb-4">
    <div class="px-4 py-3 rounded-pill bg-white border border-black border-opacity-10 d-inline-flex align-items-center gap-3">
      <div class="fw-semibold">Mes crédits</div>
      <div class="px-3 py-1 rounded-pill bg-light border fw-bold">
        <?= $credits ?>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="p-4 p-md-5 rounded-4 bg-secondary text-white border border-black border-opacity-10">

        <h2 class="h5 fw-bold text-center mb-4">À quoi servent mes crédits ?</h2>

        <div class="text-white" style="line-height:1.7;">
          <p class="mb-3">
            EcoRide fonctionne avec un système de <strong>crédits</strong> pour rendre les échanges simples,
            équitables et cohérents avec une logique de mobilité plus responsable.
          </p>

          <p class="mb-3">
            <strong>À l’inscription</strong>, tu reçois <strong>20 crédits</strong> pour démarrer et réserver tes premiers trajets.
            Ensuite, tu peux utiliser tes crédits pour réserver une place en tant que passager.
          </p>

          <p class="mb-3">
            <strong>Quand tu es conducteur</strong>, tu gagnes des crédits quand tu réalises des trajets avec des passagers.
            EcoRide applique une <strong>taxe plateforme de 2 crédits par passager</strong> : cette contribution sert à faire vivre le service
            (maintenance, sécurité, lutte contre les abus, support, amélioration continue).
          </p>

          <p class="mb-3">
            L’idée est un modèle <strong>vertueux</strong> : la plateforme est soutenue par l’usage,
            et l’économie interne récompense la participation active (proposer et réaliser des trajets),
            plutôt que d’encourager la surconsommation.
          </p>

          <p class="mb-0">
            Ton solde évoluera automatiquement à chaque réservation, annulation validée et trajet terminé (avec validation des passagers).
          </p>
        </div>

      </div>

      <div class="text-center mt-4">
        <a class="btn btn-light border fw-semibold rounded-pill px-4" href="<?= BASE_URL ?>/mon-compte">
          ← Retour sur mon compte
        </a>
      </div>
    </div>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
