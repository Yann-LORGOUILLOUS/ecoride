<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class StatsRepository
{
    public function countTripsSince(string $pivot): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM trips WHERE created_at >= :pivot');
        $stmt->execute(['pivot' => $pivot]);
        return (int)$stmt->fetchColumn();
    }

    public function tripsPerDaySince(string $pivot): array
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT DATE(created_at) AS day, COUNT(*) AS cnt
            FROM trips
            WHERE created_at >= :pivot
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ');
        $stmt->execute(['pivot' => $pivot]);

        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[(string)$r['day']] = (int)$r['cnt'];
        }
        return $out;
    }

    public function creditsCreatedTotalSince(string $pivot): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT COALESCE(SUM(amount), 0)
            FROM credits_transactions
            WHERE created_at >= :pivot AND amount > 0
        ');
        $stmt->execute(['pivot' => $pivot]);
        return (int)$stmt->fetchColumn();
    }

    public function creditsConsumedTotalSince(string $pivot): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT COALESCE(SUM(-amount), 0)
            FROM credits_transactions
            WHERE created_at >= :pivot AND amount < 0
        ');
        $stmt->execute(['pivot' => $pivot]);
        return (int)$stmt->fetchColumn();
    }

    public function creditsFlowPerDaySince(string $pivot): array
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT
                DATE(created_at) AS day,
                COALESCE(SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END), 0) AS created_sum,
                COALESCE(SUM(CASE WHEN amount < 0 THEN -amount ELSE 0 END), 0) AS consumed_sum
            FROM credits_transactions
            WHERE created_at >= :pivot
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ');
        $stmt->execute(['pivot' => $pivot]);

        $rows = $stmt->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[(string)$r['day']] = [
                'created' => (int)$r['created_sum'],
                'consumed' => (int)$r['consumed_sum'],
            ];
        }
        return $out;
    }
}
