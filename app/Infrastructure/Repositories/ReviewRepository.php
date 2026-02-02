<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class ReviewRepository
{
    public function getDriverRatingSummary(int $driverId): array
    {
        $pdo = PdoConnection::get();

        $sql = "
            SELECT
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(*) AS review_count
            FROM reviews r
            INNER JOIN trips t ON t.id = r.trip_id
            WHERE t.driver_id = :driver_id
              AND r.status = 'approved'
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':driver_id', $driverId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['avg_rating' => 0, 'review_count' => 0];

        return [
            'avg' => round((float)$row['avg_rating'], 1),
            'count' => (int)$row['review_count'],
        ];
    }

    public function findApprovedByDriverId(int $driverId, int $limit = 8): array
    {
        $pdo = PdoConnection::get();

        $sql = "
            SELECT
                r.id,
                r.rating,
                r.comment,
                r.created_at,
                u.pseudo AS author_pseudo
            FROM reviews r
            INNER JOIN trips t ON t.id = r.trip_id
            INNER JOIN users u ON u.id = r.author_id
            WHERE t.driver_id = :driver_id
              AND r.status = 'approved'
            ORDER BY r.created_at DESC
            LIMIT :limit
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':driver_id', $driverId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
