<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-4 my-lg-5">

  <div class="p-3 p-lg-4 rounded-4 bg-light border text-center mb-4">
    <h1 class="h4 mb-0 fw-bold">DASHBOARD ADMINISTRATEUR</h1>
    <div class="fw-semibold mt-2">STATISTIQUES</div>
  </div>

  <div class="row g-3 g-lg-4">
    <div class="col-12 col-lg-6">
      <div class="p-4 rounded-4 bg-light border text-center h-100">
        <div class="fw-bold">NOMBRE DE TRAJETS</div>
        <div class="mt-3">
          <div><span class="fw-semibold"><?= htmlspecialchars((string)$tripsPerDayAvg) ?></span> trajets / jour</div>
          <div class="mt-2"><span class="fw-semibold"><?= (int)$tripsTotal ?></span> trajets totaux</div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="p-4 rounded-4 bg-light border text-center h-100">
        <div class="fw-bold">GAIN PLATEFORME</div>
        <div class="mt-3">
          <div>
            <span class="fw-semibold"><?= htmlspecialchars((string)($platformCreditsPerDayAvg ?? 0)) ?></span> crédits / jour
          </div>
          <div class="mt-2">
            <span class="fw-semibold"><?= (int)($platformCreditsTotal ?? 0) ?></span> crédits gagnés au total
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="p-3 rounded-4 bg-light border h-100">
        <canvas id="tripsChart" height="160"></canvas>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="p-3 rounded-4 bg-light border h-100">
        <canvas id="creditsChart" height="160"></canvas>
      </div>
    </div>
  </div>

  <div class="text-center mt-4">
    <a class="text-decoration-none fw-semibold" href="<?= BASE_URL ?>/dashboard-administrateur">← retour au dashboard</a>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = <?= json_encode($chartLabels, JSON_UNESCAPED_SLASHES) ?>;

  new Chart(document.getElementById('tripsChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Trajets / jour',
        data: <?= json_encode($chartTrips, JSON_UNESCAPED_SLASHES) ?>,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: true } },
      scales: {
        x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
        y: { beginAtZero: true, precision: 0 }
      }
    }
  });

  new Chart(document.getElementById('creditsChart'), {
    type: 'line',
    data: {
      labels,
      datasets: [
        { label: 'Crédits gagnés par la plateforme', data: <?= json_encode($chartPlatformCredits, JSON_UNESCAPED_SLASHES) ?>, tension: 0.25 }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: true } },
      scales: {
        x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
        y: { beginAtZero: true, precision: 0 }
      }
    }
  });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
