<?php declare(strict_types=1); ?>

<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'EcoRide' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/theme.css">
</head>

<body class="bg-body-tertiary" style="font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;">

<header class="bg-ecoride-background border-bottom border-black border-opacity-10">
  <nav class="navbar navbar-expand-lg">
    <div class="container-xxl px-3">
      <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
        <img src="<?= BASE_URL ?>/assets/images/logo_EcoRide.png" alt="EcoRide" height="48">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
              aria-controls="mainNavbar" aria-expanded="false" aria-label="Ouvrir le menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <li class="nav-item"><a class="btn btn-ecoride-primary fw-bold" href="<?= BASE_URL ?>/">Accueil</a></li>
          <li class="nav-item"><a class="btn btn-ecoride-primary fw-bold" href="<?= BASE_URL ?>/trajets">Covoiturages</a></li>
          <li class="nav-item"><a class="btn btn-ecoride-primary fw-bold" href="<?= BASE_URL ?>/contact">Contact</a></li>

          <li class="nav-item d-none d-lg-block mx-1 text-secondary">|</li>
          <?php
            $isConnected = isset($_SESSION['user']);
            $userPseudo = $isConnected ? (string)$_SESSION['user']['pseudo'] : null;

            $role = $isConnected ? (string)($_SESSION['user']['role'] ?? '') : '';
            if ($role === 'employee') {
              $accountUrl = BASE_URL . '/dashboard-moderateur';
            } elseif ($role === 'admin') {
              $accountUrl = BASE_URL . '/dashboard-administrateur';
            } else {
              $accountUrl = BASE_URL . '/mon-compte';
            }
          ?>

          <?php if (!$isConnected): ?>
            <li class="nav-item">
              <a class="nav-link text-secondary px-2" href="<?= BASE_URL ?>/connexion">Connexion</a>
            </li>
            <li class="nav-item d-none d-lg-block text-secondary">|</li>
            <li class="nav-item">
              <a class="nav-link text-secondary px-2" href="<?= BASE_URL ?>/inscription">Inscription</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-secondary px-2 fw-semibold" href="<?= $accountUrl ?>">
                <?= htmlspecialchars($userPseudo ?? '', ENT_QUOTES, 'UTF-8') ?>
              </a>
            </li>
            <li class="nav-item d-none d-lg-block text-secondary">|</li>
            <li class="nav-item">
              <a class="nav-link text-danger px-2" href="<?= BASE_URL ?>/deconnexion">Se déconnecter</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main class="flex-grow-1">
