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
    
    public function countAll(): int
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->query('SELECT COUNT(*) AS cnt FROM users');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int)($row['cnt'] ?? 0);
    }

    public function searchUsers(?string $q, string $sort, string $dir): array
    {
        $pdo = PdoConnection::get();

        $allowedSort = [
            'pseudo' => 'pseudo',
            'created_at' => 'created_at',
            'role' => 'role',
            'suspended' => 'suspended',
        ];

        $sortCol = $allowedSort[$sort] ?? 'pseudo';
        $dirSql = strtolower($dir) === 'desc' ? 'DESC' : 'ASC';

        $sql = '
            SELECT id, pseudo, email, role, suspended, validated_reports_count, created_at
            FROM users
        ';

        $params = [];
        $q = trim((string)$q);

        if ($q !== '') {
            $sql .= ' WHERE pseudo LIKE :q OR email LIKE :q ';
            $params['q'] = '%' . $q . '%';
        }

        $sql .= " ORDER BY {$sortCol} {$dirSql}, id ASC ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public function setSuspended(int $userId, bool $suspended): void
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            UPDATE users
            SET suspended = :suspended
            WHERE id = :id
            LIMIT 1
        ');

        $stmt->execute([
            'suspended' => $suspended ? 1 : 0,
            'id' => $userId,
        ]);
    }
    
    public function findProfileById(int $id): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, pseudo, last_name, first_name, email, role, credits, validated_reports_count
            FROM users
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function findEditableById(int $id): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, pseudo, last_name, first_name, email, avatar_url, credits
            FROM users
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function existsByEmailExceptId(string $email, int $excludeId): bool
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT 1
            FROM users
            WHERE email = :email AND id <> :id
            LIMIT 1
        ');
        $stmt->execute([
            'email' => $email,
            'id' => $excludeId,
        ]);

        return (bool)$stmt->fetchColumn();
    }

    public function updateProfile(int $userId, array $data): void
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            UPDATE users
            SET pseudo = :pseudo,
                last_name = :last_name,
                first_name = :first_name,
                email = :email,
                avatar_url = :avatar_url
            WHERE id = :id
            LIMIT 1
        ');

        $stmt->execute([
            'pseudo' => $data['pseudo'],
            'last_name' => $data['last_name'],
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'avatar_url' => $data['avatar_url'],
            'id' => $userId,
        ]);
    }

    public function findPasswordHashById(int $id): ?string
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT password_hash
            FROM users
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);

        $hash = $stmt->fetchColumn();
        return is_string($hash) ? $hash : null;
    }

    public function updatePasswordHash(int $userId, string $passwordHash): void
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            UPDATE users
            SET password_hash = :password_hash
            WHERE id = :id
            LIMIT 1
        ');
        $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $userId,
        ]);
    }

    public function findPublicById(int $id): ?array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, pseudo, avatar_url
            FROM users
            WHERE id = :id AND suspended = 0
            LIMIT 1
        ');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function searchPublicByPseudo(string $q, int $limit = 12): array
    {
        $pdo = PdoConnection::get();

        $stmt = $pdo->prepare('
            SELECT id, pseudo, avatar_url
            FROM users
            WHERE suspended = 0
            AND pseudo LIKE :q
            ORDER BY pseudo ASC
            LIMIT :limit
        ');
        $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }
}
