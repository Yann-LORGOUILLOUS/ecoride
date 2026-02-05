<?php declare(strict_types=1);

final class IncidentRepository
{
    private string $filePath;

    public function __construct(?string $filePath = null)
    {
        $this->filePath = $filePath ?? (__DIR__ . '/../../../database/mongodb/ecoride.reports.json');
    }

    public function findPendingTripIncidents(): array
    {
        $all = $this->readAll();

        $items = [];
        foreach ($all as $doc) {
            $type = (string)($doc['type'] ?? '');
            if ($type !== 'trip_incident') { continue; }
            $status = (string)($doc['status'] ?? '');
            if ($status === 'open') { $status = 'pending'; }
            if ($status !== 'pending') { continue; }
            $items[] = $doc;
        }

        return $items;
    }

    public function updateByOid(string $oid, callable $mutator): void
    {
        $all = $this->readAll();
        $found = false;
        foreach ($all as $i => $doc) {
            if ($this->getOid($doc) !== $oid) { continue; }
            $mutator($all[$i]);
            $found = true;
            break;
        }

        if (!$found) {
            throw new RuntimeException('Incident introuvable.');
        }

        $this->writeAll($all);
    }

    public function getOid(array $doc): string
    {
        $id = $doc['_id'] ?? null;

        if (is_string($id) && $id !== '') {
            return $id;
        }

        if (is_array($id) && isset($id['$oid']) && is_string($id['$oid'])) {
            return $id['$oid'];
        }

        return '';
    }

    private function readAll(): array
    {
        if (!is_file($this->filePath)) {
            return [];
        }

        $raw = (string)file_get_contents($this->filePath);
        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }

    private function writeAll(array $docs): void
    {
        $json = json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new RuntimeException('Impossible d’encoder le JSON.');
        }

        file_put_contents($this->filePath, $json);
    }
}
