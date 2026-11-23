<?php

class Bike
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

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

    public function create(string $registration, bool $available, ?string $photo): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bikes (registration_number, availability, photo) VALUES (?, ?, ?)'
        );
        return $stmt->execute([
            $registration,
            $available ? 1 : 0,
            $photo,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM bikes WHERE bike_id = ?');
        return $stmt->execute([$id]);
    }
}
