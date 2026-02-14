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
            r.trip_id,
            r.rating,
            r.comment,
            r.created_at,
            u.pseudo AS author_pseudo,
            t.city_from,
            t.city_to,
            t.departure_datetime,
            t.arrival_datetime
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

    public function countPending(): int
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM reviews
            WHERE status = 'pending'
        ");
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function findPendingForValidation(): array
    {
        $pdo = PdoConnection::get();

        $sql = "
            SELECT
                r.id,
                r.trip_id,
                r.author_id,
                r.rating,
                r.comment,
                r.created_at,

                author.pseudo AS author_pseudo,
                'PASSAGER' AS author_role,
                author.email AS author_email,

                driver.id AS recipient_id,
                driver.pseudo AS recipient_pseudo,
                'CONDUCTEUR' AS recipient_role

            FROM reviews r
            INNER JOIN users author ON author.id = r.author_id
            INNER JOIN trips t ON t.id = r.trip_id
            INNER JOIN users driver ON driver.id = t.driver_id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approve(int $reviewId, int $validatorId): void
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            UPDATE reviews
            SET status = 'approved',
                validated_by = :validator_id,
                validated_at = NOW()
            WHERE id = :id AND status = 'pending'
        ");

        $stmt->execute([
            'validator_id' => $validatorId,
            'id' => $reviewId,
        ]);
    }

    public function reject(int $reviewId, int $validatorId): void
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            UPDATE reviews
            SET status = 'rejected',
                validated_by = :validator_id,
                validated_at = NOW()
            WHERE id = :id AND status = 'pending'
        ");

        $stmt->execute([
            'validator_id' => $validatorId,
            'id' => $reviewId,
        ]);
    }

    public function existsForTripAndAuthor(int $tripId, int $authorId): bool
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT 1
            FROM reviews
            WHERE trip_id = :trip_id
            AND author_id = :author_id
            LIMIT 1
        ");

        $stmt->execute([
            'trip_id' => $tripId,
            'author_id' => $authorId,
        ]);

        return (bool)$stmt->fetchColumn();
    }

    public function createPending(int $tripId, int $authorId, int $rating, string $comment): int
    {
        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('La note doit être comprise entre 1 et 5.');
        }

        $comment = trim($comment);
        if ($comment === '') {
            throw new InvalidArgumentException('Le commentaire est obligatoire.');
        }

        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            INSERT INTO reviews (trip_id, author_id, rating, comment, status)
            VALUES (:trip_id, :author_id, :rating, :comment, 'pending')
        ");

        $stmt->execute([
            'trip_id' => $tripId,
            'author_id' => $authorId,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        return (int)$pdo->lastInsertId();
    }
}
