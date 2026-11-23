<?php

class DashboardController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index(): void
    {
        $this->requireEmployee();
        $reservationModel = new Reservation($this->db);
        $this->view('dashboard', [
            'title' => 'Tableau de bord',
            'reservations' => $reservationModel->all('pending'),
        ]);
    }

    public function bikes(): void
    {
        $this->requireEmployee();
        $this->view('bikes', [
            'title' => 'Gestion des vélos',
            'bikes' => (new Bike($this->db))->all(),
        ]);
    }

    public function users(): void
    {
        $this->requireAdmin();
        $this->view('users', [
            'title' => 'Gestion des utilisateurs',
            'users' => (new User($this->db))->all(),
        ]);
    }

    public function allReservations(): void
    {
        $this->requireEmployee();
        $this->view('reservations', [
            'title' => 'Toutes les réservations',
            'reservations' => (new Reservation($this->db))->all(),
        ]);
    }

    public function addBike(): void
    {
        $this->requireEmployee();
        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $directory = dirname(__DIR__, 2) . '/public/uploads';
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }
                $photo = 'uploads/' . uniqid('bike_', true) . '.' . $extension;
                move_uploaded_file($_FILES['photo']['tmp_name'], dirname(__DIR__, 2) . '/public/' . $photo);
            }
        }
        (new Bike($this->db))->create($_POST, $photo);
        $this->redirect('/dashboard/bikes');
    }

    public function deleteBike(string $id): void
    {
        $this->requireAdmin();
        (new Bike($this->db))->delete((int) $id);
        $this->redirect('/dashboard/bikes');
    }

    public function deleteUser(string $id): void
    {
        $this->requireAdmin();
        (new User($this->db))->delete((int) $id);
        $this->redirect('/dashboard/users');
    }

    public function updateReservationStatus(): void
    {
        $this->requireEmployee();
        $id = (int) ($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        (new Reservation($this->db))->updateStatus($id, $status);
        $this->json(['success' => true]);
    }

    public function deleteReservation(string $id): void
    {
        $this->requireAdmin();
        (new Reservation($this->db))->delete((int) $id);
        $this->json(['success' => true]);
    }

    public function searchReservations(): void
    {
        $this->requireEmployee();
        $term = trim($_GET['q'] ?? '');
        $this->json([
            'reservations' => $term === ''
                ? (new Reservation($this->db))->all()
                : (new Reservation($this->db))->search($term),
        ]);
    }
}
