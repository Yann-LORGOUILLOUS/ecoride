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
}
