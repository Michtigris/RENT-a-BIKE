<?php

class Reservation
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

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
        $sql .= ' ORDER BY r.res_date ASC, r.reservation_id ASC';
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
             ORDER BY r.res_date ASC, r.reservation_id ASC'
        );
        $stmt->execute([$like, $like, $like, $like, $like]);
        return $stmt->fetchAll();
    }

    public function create(array $data): string
    {
        $bikeId = (int) $data['bike_id'];
        $start = $data['start_date'];
        $end = $data['end_date'];
        $stmt = $this->db->prepare('SELECT availability FROM bikes WHERE bike_id = ? FOR UPDATE');
        $stmt->execute([$bikeId]);
        if (!(int) $stmt->fetchColumn()) {
            throw new InvalidArgumentException('Ce vélo n’est plus disponible.');
        }
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM reservations
             WHERE bike_id = ? AND status IN ("pending", "accepted")
             AND start_date < ? AND end_date > ? FOR UPDATE'
        );
        $stmt->execute([$bikeId, $end, $start]);
        if ((int) $stmt->fetchColumn() > 0) {
            throw new InvalidArgumentException('Ce vélo n\'est pas disponible sur cette période.');
        }
        $resNum = 'V-' . strtoupper(bin2hex(random_bytes(9)));
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
        $stmt = $this->db->prepare("UPDATE reservations SET status = ? WHERE reservation_id = ? AND status = 'pending'");
        return $stmt->execute([$status, $id]) && $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM reservations WHERE reservation_id = ?');
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }
}
