<?php

class DashboardController extends Controller
{
    private Bike $bikeModel;
    private Reservation $reservationModel;
    private User $userModel;

    public function __construct()
    {
        $this->bikeModel = new Bike();
        $this->reservationModel = new Reservation();
        $this->userModel = new User();
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
        $this->view('bikes', ['title' => 'Gestion des vélos', 'bikes' => $this->bikeModel->all()]);
    }

    public function users(): void
    {
        $this->requireEmployee();
        $this->view('users', ['title' => 'Gestion des utilisateurs', 'users' => $this->userModel->all()]);
    }

    public function allReservations(): void
    {
        $this->requireEmployee();
        $term = $this->searchTerm();
        $this->view('reservations', [
            'title' => 'Toutes les réservations',
            'reservations' => $this->matchingReservations($term),
            'term' => $term,
        ]);
    }

    public function addBike(): void
    {
        $this->requireEmployee();
        $registration = is_string($_POST['registration_number'] ?? null) ? trim($_POST['registration_number']) : '';
        if ($registration === '' || iconv_strlen($registration, 'UTF-8') > 50) {
            $this->redirect('/dashboard/bikes', 'Indiquez une immatriculation de 50 caractères maximum.');
        }

        $photo = null;
        $directory = __DIR__ . '/../../public/';
        $upload = $_FILES['photo'] ?? null;
        if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($upload['error'] !== UPLOAD_ERR_OK) {
                $this->redirect('/dashboard/bikes', 'Le transfert de la photo a échoué.');
            }
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $image = getimagesize($upload['tmp_name']);
            $mime = $image['mime'] ?? '';
            if (!isset($extensions[$mime])) {
                $this->redirect('/dashboard/bikes', 'Choisissez une image JPEG, PNG ou WebP.');
            }
            $photo = 'uploads/bike_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
            if (!move_uploaded_file($upload['tmp_name'], $directory . $photo)) {
                $this->redirect('/dashboard/bikes', 'Impossible d’enregistrer la photo.');
            }
        }

        try {
            $this->bikeModel->create($registration, isset($_POST['availability']), $photo);
        } catch (PDOException $exception) {
            if ($photo !== null) {
                unlink($directory . $photo);
            }
            if ($exception->getCode() === '23000') {
                $this->redirect('/dashboard/bikes', 'Cette immatriculation est déjà utilisée.');
            }
            throw $exception;
        }
        $this->redirect('/dashboard/bikes', 'Vélo ajouté.');
    }

    public function deleteBike(): void
    {
        $this->requireAdmin();
        try {
            $this->bikeModel->delete((int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $this->redirect('/dashboard/bikes', 'Supprimez d’abord les réservations liées à ce vélo.');
            }
            throw $exception;
        }
        $this->redirect('/dashboard/bikes');
    }

    public function deleteUser(): void
    {
        $this->requireAdmin();
        $id = (int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id === (int) $_SESSION['user']['user_id']) {
            $this->redirect('/dashboard/users', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        try {
            $this->userModel->delete($id);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $this->redirect('/dashboard/users', 'Supprimez d’abord les réservations liées à ce compte.');
            }
            throw $exception;
        }
        $this->redirect('/dashboard/users');
    }

    public function updateReservationStatus(): void
    {
        $this->requireEmployee();
        $id = (int) filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['accepted', 'rejected'], true)) {
            $this->redirect('/dashboard', 'Choisissez une action valide.');
        }
        if (!$this->reservationModel->updateStatus($id, $status)) {
            $this->redirect('/dashboard', 'Cette demande a déjà été traitée ou supprimée.');
        }
        $this->redirect('/dashboard', $status === 'accepted' ? 'Demande acceptée.' : 'Demande refusée.');
    }

    public function deleteReservation(): void
    {
        $this->requireAdmin();
        $this->reservationModel->delete((int) filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
        $term = is_string($_POST['q'] ?? null) ? trim($_POST['q']) : '';
        $this->redirect('/dashboard/reservations?q=' . rawurlencode($term));
    }

    public function searchReservations(): void
    {
        $this->requireEmployee();
        $term = $this->searchTerm();
        $reservations = $this->matchingReservations($term);
        require __DIR__ . '/../Views/reservationRows.php';
    }

    private function searchTerm(): string
    {
        return is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
    }

    private function matchingReservations(string $term): array
    {
        if ($term === '') {
            return $this->reservationModel->all();
        }
        return $this->reservationModel->search($term);
    }
}
