<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/StatsRepository.php';

final class AdminStatsController extends BaseController
{
    private const PIVOT = '2026-02-19 00:00:00';

    public function adminStats(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        if (($_SESSION['user']['role'] ?? null) !== 'admin') {
            header('Location: ' . BASE_URL . '/mon-compte');
            exit;
        }

        $pivot = self::PIVOT;
        $repo = new StatsRepository();
        $now = new DateTimeImmutable('now');
        $pivotDt = new DateTimeImmutable($pivot);
        $daysElapsed = max(1, (int)$pivotDt->diff($now)->days);
        $tripsTotal = $repo->countTripsSince($pivot);
        $tripsPerDayAvg = round($tripsTotal / $daysElapsed, 2);
        $creditsCreatedTotal = $repo->creditsCreatedTotalSince($pivot);
        $creditsConsumedTotal = $repo->creditsConsumedTotalSince($pivot);
        $creditsCreatedPerDayAvg = round($creditsCreatedTotal / $daysElapsed, 2);
        $creditsConsumedPerDayAvg = round($creditsConsumedTotal / $daysElapsed, 2);
        $tripsPerDay = $repo->tripsPerDaySince($pivot);
        $creditsPerDay = $repo->creditsFlowPerDaySince($pivot);
        $labels = $this->buildDateLabels($pivotDt, $now);
        $tripsSeries = [];
        $creditsCreatedSeries = [];
        $creditsConsumedSeries = [];
        foreach ($labels as $d) {
            $tripsSeries[] = (int)($tripsPerDay[$d] ?? 0);
            $creditsCreatedSeries[] = (int)(($creditsPerDay[$d]['created'] ?? 0));
            $creditsConsumedSeries[] = (int)(($creditsPerDay[$d]['consumed'] ?? 0));
        }

        $this->renderView('statistiques', [
            'pageTitle' => 'Statistiques',
            'pivot' => $pivot,
            'daysElapsed' => $daysElapsed,

            'tripsTotal' => $tripsTotal,
            'tripsPerDayAvg' => $tripsPerDayAvg,

            'creditsCreatedTotal' => $creditsCreatedTotal,
            'creditsConsumedTotal' => $creditsConsumedTotal,
            'creditsCreatedPerDayAvg' => $creditsCreatedPerDayAvg,
            'creditsConsumedPerDayAvg' => $creditsConsumedPerDayAvg,

            'chartLabels' => $labels,
            'chartTrips' => $tripsSeries,
            'chartCreditsCreated' => $creditsCreatedSeries,
            'chartCreditsConsumed' => $creditsConsumedSeries,
        ]);
    }

    private function buildDateLabels(DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        $labels = [];
        $cur = $start->setTime(0, 0, 0);
        $endDay = $end->setTime(0, 0, 0);

        while ($cur <= $endDay) {
            $labels[] = $cur->format('Y-m-d');
            $cur = $cur->modify('+1 day');
        }

        return $labels;
    }
}
