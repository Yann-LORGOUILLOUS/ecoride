<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $pageTitle ?? 'EcoRide' ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/header.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/pages/accueil.css">

</head>

<body>

<header class="site-header">
  <div class="header-inner container">
    <a class="brand" href="<?= BASE_URL ?>/">
      <img
        src="<?= BASE_URL ?>/assets/images/logo_EcoRide.png"
        alt="EcoRide"
        class="brand-logo"
      >
    </a>

    <nav class="nav-links" aria-label="Navigation principale">
      <a class="nav-btn" href="<?= BASE_URL ?>/">Accueil</a>
      <a class="nav-btn" href="<?= BASE_URL ?>/trajets">Covoiturages</a>
      <a class="nav-btn" href="<?= BASE_URL ?>/contact">Contact</a>
      <a class="nav-auth" href="<?= BASE_URL ?>/connexion">Connexion</a>
      <span class="nav-sep">|</span>
      <a class="nav-auth" href="<?= BASE_URL ?>/inscription">Inscription</a>
    </nav>

    <button class="burger" type="button" aria-label="Ouvrir le menu">
      <span class="burger-lines"></span>
    </button>
  </div>
</header>

<main>
