<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, pseudo, email, password_hash, role, suspended
            FROM users
            WHERE email = :email
            LIMIT 1
        ');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();
        return is_array($user) ? $user : null;
    }

    public function existsByEmail(string $email): bool
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return (bool)$stmt->fetchColumn();
    }

    public function createUser(array $data): int
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            INSERT INTO users (pseudo, last_name, first_name, email, password_hash, avatar_url, role, credits, suspended)
            VALUES (:pseudo, :last_name, :first_name, :email, :password_hash, :avatar_url, :role, :credits, :suspended)
        ');

        $stmt->execute([
            'pseudo' => $data['pseudo'],
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'avatar_url' => $data['avatar_url'],
            'role' => $data['role'],
            'credits' => $data['credits'],
            'suspended' => $data['suspended'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, pseudo, email, role, validated_reports_count
            FROM users
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function incrementValidatedReportsCount(int $userId): void
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            UPDATE users
            SET validated_reports_count = validated_reports_count + 1
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $userId]);
    }

}
