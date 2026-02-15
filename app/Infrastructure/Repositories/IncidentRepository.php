<?php declare(strict_types=1);

use App\Infrastructure\Database\MongoConnection;
use MongoDB\BSON\ObjectId;

final class IncidentRepository
{
    private string $collectionName;

    public function __construct(?string $collectionName = null)
    {
        $this->collectionName = $collectionName
            ?? (string)($_ENV['MONGODB_COLLECTION'] ?? 'reports');
    }

    public function findPendingTripIncidents(): array
    {
        $collection = $this->getCollection();

        $cursor = $collection->find(
            [
                'type' => 'trip_incident',
                'status' => ['$in' => ['pending', 'open']],
            ],
            [
                'sort' => ['created_at' => -1],
                'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
            ]
        );

        $items = [];
        foreach ($cursor as $doc) {
            if (!is_array($doc)) { continue; }
            if (($doc['status'] ?? null) === 'open') {
                $doc['status'] = 'pending';
            }

            $items[] = $doc;
        }

        return $items;
    }

    public function countPendingTripIncidents(): int
    {
        $collection = $this->getCollection();

        return $collection->countDocuments([
            'type' => 'trip_incident',
            'status' => ['$in' => ['pending', 'open']],
        ]);
    }

    public function updateByOid(string $oid, callable $mutator): void
    {
        $collection = $this->getCollection();
        $objectId = $this->toObjectId($oid);

        $doc = $collection->findOne(
            ['_id' => $objectId],
            ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]
        );

        if (!is_array($doc)) {
            throw new RuntimeException('Incident introuvable.');
        }

        $mutator($doc);

        unset($doc['_id']);

        $collection->updateOne(
            ['_id' => $objectId],
            ['$set' => $doc]
        );
    }

    public function getOid(array $doc): string
    {
        $id = $doc['_id'] ?? null;

        if ($id instanceof ObjectId) {
            return (string)$id;
        }

        if (is_string($id) && $id !== '') {
            return $id;
        }

        if (is_array($id) && isset($id['$oid']) && is_string($id['$oid'])) {
            return $id['$oid'];
        }

        return '';
    }

    private function getCollection()
    {
        $db = MongoConnection::getDatabase();
        return $db->selectCollection($this->collectionName);
    }

    private function toObjectId(string $oid): ObjectId
    {
        $oid = trim($oid);

        if (!preg_match('/^[a-f0-9]{24}$/i', $oid)) {
            throw new RuntimeException('Identifiant MongoDB invalide.');
        }

        return new ObjectId($oid);
    }

    public function countPendingTechnicalReports(): int
    {
        $collection = $this->getCollection();

        return $collection->countDocuments([
            'type' => 'app_issue',
            'status' => ['$in' => ['pending', 'open']],
        ]);
    }

    public function findPendingAppIssues(): array
    {
        $collection = $this->getCollection();

        $cursor = $collection->find(
            [
                'type' => 'app_issue',
                'status' => ['$in' => ['pending', 'open']],
            ],
            [
                'sort' => ['created_at' => -1],
                'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
            ]
        );

        $items = [];
        foreach ($cursor as $doc) {
            if (!is_array($doc)) { continue; }
            if (($doc['status'] ?? null) === 'open') {
                $doc['status'] = 'pending';
            }
            $items[] = $doc;
        }

        return $items;
    }

    public function closeAppIssue(string $oid, int $adminUserId, string $decision = 'validated', ?string $reason = null): void
    {
        $this->updateByOid($oid, function (&$doc) use ($adminUserId, $decision, $reason) {
            $doc['status'] = 'resolved';

            $doc['moderation'] ??= [];
            $doc['moderation']['handled_by_employee_id'] = $adminUserId;
            $doc['moderation']['handled_at'] = gmdate('c');
            $doc['moderation']['decision'] = $decision;
            $doc['moderation']['decision_reason'] = $reason;
        });
    }

    public function createAppIssue(int $reporterUserId, string $page, string $subject, string $severity, string $comment): string
    {
        $page = trim($page);
        $subject = trim($subject);
        $severity = trim($severity);
        $comment = trim($comment);

        if ($page === '' || $subject === '' || $severity === '' || $comment === '') {
            throw new InvalidArgumentException('Champs obligatoires manquants.');
        }

        $allowedSeverities = ['Faible', 'Moyenne', 'Élevée', 'Critique'];
        if (!in_array($severity, $allowedSeverities, true)) {
            throw new InvalidArgumentException('Gravité invalide.');
        }

        $collection = $this->getCollection();

        $doc = [
            'type' => 'app_issue',
            'status' => 'pending',
            'reporter_user_id' => $reporterUserId,
            'page' => $page,
            'subject' => $subject,
            'severity' => $severity,
            'comment' => $comment,
            'created_at' => gmdate('c'),
        ];

        $result = $collection->insertOne($doc);

        return (string)$result->getInsertedId();
    }
}
