<?php

class User
{
    public function __construct(private PDO $db) {}

    public function all(): array
    {
        $stmt = $this->db->query('SELECT user_id, firstname, lastname, login, role FROM users ORDER BY lastname, firstname');
        return $stmt->fetchAll();
    }

    public function findByLogin(int $login): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE login = ?');
        $stmt->execute([$login]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (firstname, lastname, login, password, role) VALUES (?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            trim($data['firstname']),
            trim($data['lastname']),
            (int) $data['login'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'citizen',
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE user_id = ?');
        return $stmt->execute([$id]);
    }
}
