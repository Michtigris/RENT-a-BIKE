<?php

class Reservation
{
    public function __construct(private PDO $db) {}

    public function all(?string $status = null): array
    {
        $sql = 'SELECT r.*, b.registration_number, b.photo, u.firstname, u.lastname, u.login
                FROM reservations r
                JOIN bikes b ON b.bike_id = r.bike_id
                JOIN users u ON u.user_id = r.user_id';
        $params = [];
        if ($status !== null) {
            $sql .= ' WHERE r.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY r.res_date ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function search(string $term): array
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->prepare(
            'SELECT r.*, b.registration_number, b.photo, u.firstname, u.lastname, u.login
             FROM reservations r
             JOIN bikes b ON b.bike_id = r.bike_id
             JOIN users u ON u.user_id = r.user_id
             WHERE r.res_num LIKE ? OR u.firstname LIKE ? OR u.lastname LIKE ?
                OR u.login LIKE ? OR b.registration_number LIKE ?
             ORDER BY r.res_date ASC'
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): string
    {
        $bikeId = (int) $data['bike_id'];
        $start = $data['start_date'];
        $end = $data['end_date'];
        if (strtotime($end) <= strtotime($start)) {
            throw new InvalidArgumentException('La date de fin doit être après la date de début.');
        }
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM reservations
             WHERE bike_id = ? AND status != "rejected"
             AND start_date < ? AND end_date > ?'
        );
        $stmt->execute([$bikeId, $end, $start]);
        if ((int) $stmt->fetchColumn() > 0) {
            throw new InvalidArgumentException('Ce vélo n\'est pas disponible sur cette période.');
        }
        $resNum = 'V-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $stmt = $this->db->prepare(
            'INSERT INTO reservations (user_id, bike_id, res_num, start_date, end_date)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([(int) $data['user_id'], $bikeId, $resNum, $start, $end]);
        return $resNum;
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['accepted', 'rejected'], true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE reservations SET status = ? WHERE reservation_id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM reservations WHERE reservation_id = ?');
        return $stmt->execute([$id]);
    }
}
