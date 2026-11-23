<?php

class Bike
{
    public function __construct(private PDO $db) {}

    public function all(bool $availableOnly = false): array
    {
        $sql = 'SELECT * FROM bikes';
        if ($availableOnly) {
            $sql .= ' WHERE availability = 1';
        }
        $sql .= ' ORDER BY registration_number';
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM bikes WHERE bike_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data, ?string $photo = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bikes (registration_number, availability, photo) VALUES (?, ?, ?)'
        );
        return $stmt->execute([
            trim($data['registration_number']),
            isset($data['availability']) ? 1 : 0,
            $photo,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM bikes WHERE bike_id = ?');
        return $stmt->execute([$id]);
    }
}
