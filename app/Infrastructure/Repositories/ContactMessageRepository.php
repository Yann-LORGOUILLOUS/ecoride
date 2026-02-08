<?php declare(strict_types=1);

require_once __DIR__ . '/../Database/PdoConnection.php';

final class ContactMessageRepository
{
    public function create(string $name, string $email, string $subject, string $message): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            INSERT INTO contact_messages (name, email, subject, message)
            VALUES (:name, :email, :subject, :message)
        ');
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        ]);

        return (int)$pdo->lastInsertId();
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('
            SELECT id, name, email, subject, message, status, created_at
            FROM contact_messages
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): void
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('UPDATE contact_messages SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function countByStatus(string $status): int
    {
        $pdo = PdoConnection::get();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM contact_messages WHERE status = :status');
        $stmt->execute(['status' => $status]);
        return (int)$stmt->fetchColumn();
    }
}
