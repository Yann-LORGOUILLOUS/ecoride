<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class TripRepository
{
    public function searchPaginated(
        ?string $cityFrom,
        ?string $cityTo,
        ?string $date,
        string $sort,
        int $limit,
        int $offset
    ): array {
        $pdo = PdoConnection::get();

        $whereSql = " WHERE t.status = 'planned' AND t.seats_available >= 1 ";
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
}
