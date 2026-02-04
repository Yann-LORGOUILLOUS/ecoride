<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class ReservationRepository
{
    public function userHasConfirmedReservation(int $tripId, int $userId): bool
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT 1
            FROM reservations
            WHERE trip_id = :trip_id AND user_id = :user_id AND status = 'confirmed'
            LIMIT 1
        ");
        $stmt->execute([
            'trip_id' => $tripId,
            'user_id' => $userId,
        ]);

        return (bool)$stmt->fetchColumn();
    }

    public function reserveTrip(int $tripId, int $userId): void
    {
        $pdo = PdoConnection::get();

        try {
            $pdo->beginTransaction();

            $tripStmt = $pdo->prepare("
                SELECT id, driver_id, price_credits, seats_available, status
                FROM trips
                WHERE id = :trip_id
                FOR UPDATE
            ");
            $tripStmt->execute(['trip_id' => $tripId]);
            $trip = $tripStmt->fetch();

            if (!is_array($trip)) {
                throw new RuntimeException('Trajet introuvable.');
            }

            if (($trip['status'] ?? '') !== 'planned') {
                throw new RuntimeException('Ce trajet n’est pas réservable.');
            }

            if ((int)$trip['seats_available'] < 1) {
                throw new RuntimeException('Plus de places disponibles.');
            }

            if ((int)$trip['driver_id'] === $userId) {
                throw new RuntimeException('Vous ne pouvez pas réserver votre propre trajet.');
            }

            $userStmt = $pdo->prepare("
                SELECT id, credits
                FROM users
                WHERE id = :user_id
                FOR UPDATE
            ");
            $userStmt->execute(['user_id' => $userId]);
            $user = $userStmt->fetch();

            if (!is_array($user)) {
                throw new RuntimeException('Utilisateur introuvable.');
            }

            $price = (int)$trip['price_credits'];
            $credits = (int)$user['credits'];

            if ($credits < $price) {
                throw new RuntimeException('Crédits insuffisants pour réserver ce trajet.');
            }

            $dupStmt = $pdo->prepare("
                SELECT 1
                FROM reservations
                WHERE trip_id = :trip_id AND user_id = :user_id
                LIMIT 1
            ");
            $dupStmt->execute([
                'trip_id' => $tripId,
                'user_id' => $userId,
            ]);
            if ($dupStmt->fetchColumn()) {
                throw new RuntimeException('Vous avez déjà réservé ce trajet.');
            }

            $insRes = $pdo->prepare("
                INSERT INTO reservations (trip_id, user_id, status)
                VALUES (:trip_id, :user_id, 'confirmed')
            ");
            $insRes->execute([
                'trip_id' => $tripId,
                'user_id' => $userId,
            ]);

            $updTrip = $pdo->prepare("
                UPDATE trips
                SET seats_available = seats_available - 1
                WHERE id = :trip_id
            ");
            $updTrip->execute(['trip_id' => $tripId]);

            $updCredits = $pdo->prepare("
                UPDATE users
                SET credits = credits - :price
                WHERE id = :user_id
            ");
            $updCredits->execute([
                'price' => $price,
                'user_id' => $userId,
            ]);

            $insTx = $pdo->prepare("
                INSERT INTO credits_transactions (user_id, trip_id, type, amount, comment)
                VALUES (:user_id, :trip_id, :type, :amount, :comment)
            ");
            $insTx->execute([
                'user_id' => $userId,
                'trip_id' => $tripId,
                'type' => 'reservation',
                'amount' => -$price,
                'comment' => 'Réservation du trajet',
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new RuntimeException($e->getMessage());
        }
    }
}
