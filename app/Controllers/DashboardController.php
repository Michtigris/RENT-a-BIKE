<?php

class DashboardController extends Controller
{
    private Bike $bikeModel;
    private Reservation $reservationModel;
    private User $userModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->bikeModel = new Bike($db);
        $this->reservationModel = new Reservation($db);
        $this->userModel = new User($db);
    }

    public function index(): void
    {
        $this->requireEmployee();
        $this->view('dashboard', [
            'title' => 'Tableau de bord',
            'reservations' => $this->reservationModel->all('pending'),
        ]);
    }

    public function bikes(): void
    {
        $this->requireEmployee();
        $this->view('bikes', [
            'title' => 'Gestion des vélos',
            'bikes' => $this->bikeModel->all(),
        ]);
    }

    public function users(): void
    {
        $this->requireEmployee();
        $this->view('users', [
            'title' => 'Gestion des utilisateurs',
            'users' => $this->userModel->all(),
        ]);
    }

    public function allReservations(): void
    {
        $this->requireEmployee();
        $this->view('reservations', [
            'title' => 'Toutes les réservations',
            'reservations' => $this->reservationModel->all(),
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
        $this->bikeModel->create($_POST, $photo);
        $this->redirect('/dashboard/bikes');
    }

    public function deleteBike(string $id): void
    {
        $this->requireAdmin();
        $this->bikeModel->delete((int) $id);
        $this->redirect('/dashboard/bikes');
    }

    public function deleteUser(string $id): void
    {
        $this->requireAdmin();
        $this->userModel->delete((int) $id);
        $this->redirect('/dashboard/users');
    }

    public function updateReservationStatus(): void
    {
        $this->requireEmployee();
        $id = (int) ($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['accepted', 'rejected'], true)) {
            $this->json(['success' => false], 422);
        }
        if (!$this->reservationModel->updateStatus($id, $status)) {
            $this->json(['success' => false], 404);
        }
        $this->json(['success' => true]);
    }

    public function deleteReservation(string $id): void
    {
        $this->requireAdmin();
        if (!$this->reservationModel->delete((int) $id)) {
            $this->json(['success' => false], 404);
        }
        $this->json(['success' => true]);
    }

    public function searchReservations(): void
    {
        $this->requireEmployee();
        $term = trim($_GET['q'] ?? '');
        $reservations = $term === ''
            ? $this->reservationModel->all()
            : $this->reservationModel->search($term);
        $this->json(['reservations' => $reservations]);
    }
}
