<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class TripRepository
{
    private const PLATFORM_USER_ID = 1;
    private const PLATFORM_FEE_CREDITS = 2;
    
    public function searchPaginated(
        ?string $cityFrom,
        ?string $cityTo,
        ?string $date,
        string $sort,
        int $limit,
        int $offset
    ): array {
        $pdo = PdoConnection::get();

        $whereSql = " WHERE t.status = 'planned' AND t.seats_available >= 1 AND t.price_credits >= 3 ";
        $params = [];

        if ($cityFrom !== null && $cityFrom !== '') {
            $whereSql .= " AND t.city_from LIKE :city_from";
            $params['city_from'] = '%' . $cityFrom . '%';
        }

        if ($cityTo !== null && $cityTo !== '') {
            $whereSql .= " AND t.city_to LIKE :city_to";
            $params['city_to'] = '%' . $cityTo . '%';
        }

        if ($date !== null && $date !== '') {
            $whereSql .= " AND DATE(t.departure_datetime) = :departure_date";
            $params['departure_date'] = $date;
        }

        $countSql = "
            SELECT COUNT(*) AS total
            FROM trips t
            $whereSql
        ";

        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)($countStmt->fetch()['total'] ?? 0);

        $dataSql = "
            SELECT
                t.id,
                t.city_from,
                t.city_to,
                t.departure_datetime,
                t.arrival_datetime,
                t.price_credits,
                t.seats_available,
                t.smoking_allowed,
                t.pets_allowed,
                t.driver_notes,
                t.status,
                u.pseudo AS driver_pseudo,
                v.brand AS vehicle_brand,
                v.model AS vehicle_model,
                v.energy_type AS vehicle_energy
            FROM trips t
            INNER JOIN users u ON u.id = t.driver_id
            INNER JOIN vehicules v ON v.id = t.vehicule_id
            $whereSql
            " . $this->orderBy($sort) . "
            LIMIT :limit OFFSET :offset
        ";

        $dataStmt = $pdo->prepare($dataSql);

        foreach ($params as $k => $v) {
            $dataStmt->bindValue(':' . $k, $v, PDO::PARAM_STR);
        }

        $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $dataStmt->execute();
        $items = $dataStmt->fetchAll();

        return ['items' => $items, 'total' => $total];
    }

    public function findDetailsById(int $tripId): ?array
    {
        $pdo = PdoConnection::get();

        $sql = "
            SELECT
                t.id,
                t.driver_id,
                t.vehicule_id,
                t.city_from,
                t.city_to,
                t.departure_datetime,
                t.arrival_datetime,
                t.price_credits,
                t.seats_available,
                t.smoking_allowed,
                t.pets_allowed,
                t.driver_notes,
                t.status,

                u.pseudo AS driver_pseudo,
                u.email AS driver_email,
                u.avatar_url AS driver_avatar_url,
                u.created_at AS driver_created_at,

                (
                    SELECT COUNT(*)
                    FROM trips t2
                    WHERE t2.driver_id = t.driver_id
                ) AS driver_trips_count,

                v.brand AS vehicle_brand,
                v.model AS vehicle_model,
                v.energy_type AS vehicle_energy,
                v.seats_total AS vehicle_seats_total

            FROM trips t
            INNER JOIN users u ON u.id = t.driver_id
            INNER JOIN vehicules v ON v.id = t.vehicule_id
            WHERE t.id = :trip_id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function createTrip(array $data): int
{
    $pdo = PdoConnection::get();

    $stmt = $pdo->prepare('
        INSERT INTO trips (
            driver_id,
            vehicule_id,
            city_from,
            city_to,
            departure_datetime,
            arrival_datetime,
            price_credits,
            seats_available,
            smoking_allowed,
            pets_allowed,
            driver_notes,
            status
        ) VALUES (
            :driver_id,
            :vehicule_id,
            :city_from,
            :city_to,
            :departure_datetime,
            :arrival_datetime,
            :price_credits,
            :seats_available,
            :smoking_allowed,
            :pets_allowed,
            :driver_notes,
            :status
        )
    ');

    $stmt->execute([
        'driver_id' => $data['driver_id'],
        'vehicule_id' => $data['vehicule_id'],
        'city_from' => $data['city_from'],
        'city_to' => $data['city_to'],
        'departure_datetime' => $data['departure_datetime'],
        'arrival_datetime' => $data['arrival_datetime'],
        'price_credits' => $data['price_credits'],
        'seats_available' => $data['seats_available'],
        'smoking_allowed' => $data['smoking_allowed'],
        'pets_allowed' => $data['pets_allowed'],
        'driver_notes' => $data['driver_notes'],
        'status' => $data['status'],
    ]);

    return (int)$pdo->lastInsertId();
}
    
    private function orderBy(string $sort): string
    {
        return match ($sort) {
            'price_asc'  => " ORDER BY t.price_credits ASC, t.departure_datetime ASC",
            'price_desc' => " ORDER BY t.price_credits DESC, t.departure_datetime ASC",
            'seats_desc' => " ORDER BY t.seats_available DESC, t.departure_datetime ASC",
            'date_desc'  => " ORDER BY t.departure_datetime DESC",
            default      => " ORDER BY t.departure_datetime ASC",
        };
    }

    public function moderateTrip(int $tripId, string $status, ?int $priceCredits): void
    {
        $allowed = ['planned', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException('Invalid status.');
        }

        $pdo = PdoConnection::get();

        if ($status === 'planned') {

            $check = $pdo->prepare("
                SELECT price_credits
                FROM trips
                WHERE id = :id AND status = 'pending'
                LIMIT 1
            ");
            $check->execute(['id' => $tripId]);
            $row = $check->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                throw new InvalidArgumentException('Trip not found or not pending.');
            }

            $currentPrice = (int)$row['price_credits'];
            if ($currentPrice < 3) {
                throw new InvalidArgumentException('Trip price must be at least 3 credits.');
            }

            $stmt = $pdo->prepare("
                UPDATE trips
                SET status = 'planned'
                WHERE id = :id AND status = 'pending'
            ");
            $stmt->execute(['id' => $tripId]);
            return;
        }

        $stmt = $pdo->prepare("
            UPDATE trips
            SET status = 'cancelled'
            WHERE id = :id AND status IN ('pending', 'planned')
        ");
        $stmt->execute(['id' => $tripId]);
    }

    public function findPendingForValidation(): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT
                t.id,
                t.created_at,
                t.departure_datetime,
                u.pseudo AS driver_pseudo
            FROM trips t
            INNER JOIN users u ON u.id = t.driver_id
            WHERE t.status = 'pending'
            ORDER BY t.created_at DESC
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPending(): int
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM trips 
            WHERE status = 'pending'
        ");
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function findDriverActiveTrips(int $driverId): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT
                id,
                city_from,
                city_to,
                departure_datetime,
                arrival_datetime,
                price_credits,
                seats_available,
                status
            FROM trips
            WHERE driver_id = :driver_id
            AND status IN ('pending','planned','ongoing')
            ORDER BY departure_datetime ASC
        ");

        $stmt->execute(['driver_id' => $driverId]);
        $rows = $stmt->fetchAll();

        return is_array($rows) ? $rows : [];
    }

    public function updateStatusForDriver(int $tripId, int $driverId, string $fromStatus, string $toStatus): bool
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            UPDATE trips
            SET status = :to_status
            WHERE id = :id
            AND driver_id = :driver_id
            AND status = :from_status
            LIMIT 1
        ");

        $stmt->execute([
            'to_status' => $toStatus,
            'id' => $tripId,
            'driver_id' => $driverId,
            'from_status' => $fromStatus,
        ]);

        return $stmt->rowCount() === 1;
    }

    public function cancelTripForDriver(int $tripId, int $driverId): bool
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            UPDATE trips
            SET status = 'cancelled'
            WHERE id = :id
            AND driver_id = :driver_id
            AND status IN ('pending','planned')
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $tripId,
            'driver_id' => $driverId,
        ]);

        return $stmt->rowCount() === 1;
    }

    public function findPassengerContactsForTrip(int $tripId): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT u.email, u.pseudo
            FROM reservations r
            INNER JOIN users u ON u.id = r.user_id
            WHERE r.trip_id = :trip_id
            AND r.status = 'confirmed'
        ");

        $stmt->execute(['trip_id' => $tripId]);
        $rows = $stmt->fetchAll();

        return is_array($rows) ? $rows : [];
    }

    public function findTripBasicById(int $tripId): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT id, city_from, city_to, departure_datetime, arrival_datetime, status
            FROM trips
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $tripId]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }

    public function findFinishedByDriverId(int $driverId, int $limit = 10): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare("
            SELECT
                id,
                city_from,
                city_to,
                departure_datetime,
                arrival_datetime,
                price_credits
            FROM trips
            WHERE driver_id = :driver_id
            AND status = 'finished'
            ORDER BY departure_datetime DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':driver_id', $driverId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function settleTripCreditsOnFinish(int $tripId, int $driverId): void
    {
        $pdo = PdoConnection::get();

        try {
            $pdo->beginTransaction();

            $tripStmt = $pdo->prepare("
                SELECT id, driver_id, price_credits, status
                FROM trips
                WHERE id = :trip_id
                FOR UPDATE
            ");
            $tripStmt->execute(['trip_id' => $tripId]);
            $trip = $tripStmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($trip)) {
                throw new RuntimeException('Trajet introuvable.');
            }

            if ((int)$trip['driver_id'] !== $driverId) {
                throw new RuntimeException('Action non autorisée.');
            }

            if ((string)$trip['status'] !== 'finished') {
                throw new RuntimeException('Le trajet doit être clôturé pour déclencher le paiement.');
            }

            $price = (int)$trip['price_credits'];
            if ($price < 3) {
                throw new RuntimeException('Prix invalide : paiement impossible.');
            }

            $alreadyStmt = $pdo->prepare("
                SELECT 1
                FROM credits_transactions
                WHERE trip_id = :trip_id
                AND type IN ('driver_payout', 'platform_fee')
                LIMIT 1
            ");
            $alreadyStmt->execute(['trip_id' => $tripId]);
            if ($alreadyStmt->fetchColumn()) {
                $pdo->commit();
                return;
            }

            $countStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM reservations
                WHERE trip_id = :trip_id
                AND status = 'confirmed'
            ");
            $countStmt->execute(['trip_id' => $tripId]);
            $confirmed = (int)$countStmt->fetchColumn();

            if ($confirmed <= 0) {
                $pdo->commit();
                return;
            }

            $netPerSeat = $price - self::PLATFORM_FEE_CREDITS;
            if ($netPerSeat < 0) {
                $netPerSeat = 0;
            }

            $driverGain = $netPerSeat * $confirmed;
            $platformGain = self::PLATFORM_FEE_CREDITS * $confirmed;

            if ($driverGain > 0) {
                $updDriver = $pdo->prepare("
                    UPDATE users
                    SET credits = credits + :amount
                    WHERE id = :user_id
                    LIMIT 1
                ");
                $updDriver->execute([
                    'amount' => $driverGain,
                    'user_id' => $driverId,
                ]);

                $insDriverTx = $pdo->prepare("
                    INSERT INTO credits_transactions (user_id, trip_id, type, amount, comment)
                    VALUES (:user_id, :trip_id, :type, :amount, :comment)
                ");
                $insDriverTx->execute([
                    'user_id' => $driverId,
                    'trip_id' => $tripId,
                    'type' => 'driver_payout',
                    'amount' => $driverGain,
                    'comment' => 'Paiement conducteur (trajet terminé)',
                ]);
            }

            if ($platformGain > 0) {
                $updPlatform = $pdo->prepare("
                    UPDATE users
                    SET credits = credits + :amount
                    WHERE id = :user_id
                    LIMIT 1
                ");
                $updPlatform->execute([
                    'amount' => $platformGain,
                    'user_id' => self::PLATFORM_USER_ID,
                ]);

                $insPlatformTx = $pdo->prepare("
                    INSERT INTO credits_transactions (user_id, trip_id, type, amount, comment)
                    VALUES (:user_id, :trip_id, :type, :amount, :comment)
                ");
                $insPlatformTx->execute([
                    'user_id' => self::PLATFORM_USER_ID,
                    'trip_id' => $tripId,
                    'type' => 'platform_fee',
                    'amount' => $platformGain,
                    'comment' => 'Taxe plateforme (2 crédits / réservation)',
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new RuntimeException($e->getMessage());
        }
    }
}
