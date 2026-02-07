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

    public function findConfirmedReservationsByUserId(int $userId): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT
                r.id AS reservation_id,
                r.trip_id,
                r.created_at AS reserved_at,
                t.city_from,
                t.city_to,
                t.departure_datetime,
                t.arrival_datetime,
                t.price_credits,
                t.status AS trip_status,
                u.id AS driver_id,
                u.pseudo AS driver_pseudo,
                u.email AS driver_email
            FROM reservations r
            INNER JOIN trips t ON t.id = r.trip_id
            INNER JOIN users u ON u.id = t.driver_id
            WHERE r.user_id = :user_id
            AND r.status = 'confirmed'
            ORDER BY t.departure_datetime ASC
        ");

        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();

        return is_array($rows) ? $rows : [];
    }

    public function cancelReservationWithRefund(int $reservationId, int $userId): array
    {
        $pdo = PdoConnection::get();

        try {
            $pdo->beginTransaction();

            $resStmt = $pdo->prepare("
                SELECT id, trip_id, user_id, status
                FROM reservations
                WHERE id = :id
                FOR UPDATE
            ");
            $resStmt->execute(['id' => $reservationId]);
            $reservation = $resStmt->fetch();

            if (!is_array($reservation)) {
                throw new RuntimeException('Réservation introuvable.');
            }

            if ((int)$reservation['user_id'] !== $userId) {
                throw new RuntimeException('Action non autorisée.');
            }

            if (($reservation['status'] ?? '') !== 'confirmed') {
                throw new RuntimeException('Cette réservation n’est pas annulable.');
            }

            $tripId = (int)$reservation['trip_id'];

            $tripStmt = $pdo->prepare("
                SELECT id, driver_id, price_credits, status, departure_datetime, city_from, city_to
                FROM trips
                WHERE id = :trip_id
                FOR UPDATE
            ");
            $tripStmt->execute(['trip_id' => $tripId]);
            $trip = $tripStmt->fetch();

            if (!is_array($trip)) {
                throw new RuntimeException('Trajet introuvable.');
            }

            $tripStatus = (string)($trip['status'] ?? '');
            if (!in_array($tripStatus, ['pending', 'planned'], true)) {
                throw new RuntimeException('Impossible d’annuler : le trajet a déjà démarré ou est terminé.');
            }

            $price = (int)$trip['price_credits'];

            $updRes = $pdo->prepare("
                UPDATE reservations
                SET status = 'cancelled'
                WHERE id = :id
                LIMIT 1
            ");
            $updRes->execute(['id' => $reservationId]);

            $updTrip = $pdo->prepare("
                UPDATE trips
                SET seats_available = seats_available + 1
                WHERE id = :trip_id
                LIMIT 1
            ");
            $updTrip->execute(['trip_id' => $tripId]);

            $updCredits = $pdo->prepare("
                UPDATE users
                SET credits = credits + :amount
                WHERE id = :user_id
                LIMIT 1
            ");
            $updCredits->execute([
                'amount' => $price,
                'user_id' => $userId,
            ]);

            $insTx = $pdo->prepare("
                INSERT INTO credits_transactions (user_id, trip_id, type, amount, comment)
                VALUES (:user_id, :trip_id, :type, :amount, :comment)
            ");
            $insTx->execute([
                'user_id' => $userId,
                'trip_id' => $tripId,
                'type' => 'reservation_cancel',
                'amount' => $price,
                'comment' => 'Annulation réservation (remboursement)',
            ]);

            $driverStmt = $pdo->prepare("
                SELECT id, pseudo, email
                FROM users
                WHERE id = :id
                LIMIT 1
            ");
            $driverStmt->execute(['id' => (int)$trip['driver_id']]);
            $driver = $driverStmt->fetch();

            $pdo->commit();

            return [
                'trip' => [
                    'id' => $tripId,
                    'city_from' => (string)($trip['city_from'] ?? ''),
                    'city_to' => (string)($trip['city_to'] ?? ''),
                    'departure_datetime' => (string)($trip['departure_datetime'] ?? ''),
                ],
                'driver' => is_array($driver) ? [
                    'id' => (int)($driver['id'] ?? 0),
                    'pseudo' => (string)($driver['pseudo'] ?? ''),
                    'email' => (string)($driver['email'] ?? ''),
                ] : [
                    'id' => 0, 'pseudo' => '', 'email' => ''
                ],
                'refund' => $price,
            ];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new RuntimeException($e->getMessage());
        }
    }
}
