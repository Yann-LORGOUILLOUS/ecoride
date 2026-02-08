<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class VehiculeRepository
{
    public function findByUserId(int $userId): array
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT id, license_plate, first_registration_date, brand, model, color, energy_type, seats_total, seats_available_default, created_at
            FROM vehicules
            WHERE user_id = :user_id
            ORDER BY id DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public function findOwnedById(int $vehiculeId, int $userId): ?array
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT id, license_plate, first_registration_date, brand, model, color, energy_type, seats_total, seats_available_default, created_at
            FROM vehicules
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ');
        $stmt->execute(['id' => $vehiculeId, 'user_id' => $userId]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function create(int $userId, array $data): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            INSERT INTO vehicules
              (user_id, license_plate, first_registration_date, brand, model, color, energy_type, seats_total, seats_available_default)
            VALUES
              (:user_id, :license_plate, :first_registration_date, :brand, :model, :color, :energy_type, :seats_total, :seats_available_default)
        ');

        $stmt->execute([
            'user_id' => $userId,
            'license_plate' => $data['license_plate'],
            'first_registration_date' => $data['first_registration_date'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'color' => $data['color'],
            'energy_type' => $data['energy_type'],
            'seats_total' => $data['seats_total'],
            'seats_available_default' => $data['seats_available_default'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function updateOwned(int $vehiculeId, int $userId, array $data): void
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            UPDATE vehicules
            SET
              license_plate = :license_plate,
              first_registration_date = :first_registration_date,
              brand = :brand,
              model = :model,
              color = :color,
              energy_type = :energy_type,
              seats_total = :seats_total,
              seats_available_default = :seats_available_default
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ');

        $stmt->execute([
            'id' => $vehiculeId,
            'user_id' => $userId,
            'license_plate' => $data['license_plate'],
            'first_registration_date' => $data['first_registration_date'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'color' => $data['color'],
            'energy_type' => $data['energy_type'],
            'seats_total' => $data['seats_total'],
            'seats_available_default' => $data['seats_available_default'],
        ]);
    }

    public function deleteOwned(int $vehiculeId, int $userId): void
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            DELETE FROM vehicules
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ');
        $stmt->execute(['id' => $vehiculeId, 'user_id' => $userId]);
    }

    public function countByUserId(int $userId): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM vehicules WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);

        return (int)$stmt->fetchColumn();
    }
}
