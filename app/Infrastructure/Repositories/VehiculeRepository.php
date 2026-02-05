<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class VehiculeRepository
{
    public function findByUserId(int $userId): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, brand, model, energy_type, seats_total
            FROM vehicules
            WHERE user_id = :user_id
            ORDER BY id DESC
        ');
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function findOwnedById(int $vehiculeId, int $userId): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, brand, model, energy_type, seats_total
            FROM vehicules
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ');
        $stmt->execute(['id' => $vehiculeId, 'user_id' => $userId]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }
}
