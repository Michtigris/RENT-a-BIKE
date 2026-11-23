<?php

class PublicController extends Controller
{
    public function home(): void
    {
        $bike = new Bike(Database::getInstance()->getConnection());
        $this->view('home', ['title' => 'Vélos disponibles', 'bikes' => $bike->all(true)]);
    }

    public function reserve(): void
    {
        $db = Database::getInstance()->getConnection();
        $bikeId = (int) ($_GET['bike'] ?? 0);
        $bikeModel = new Bike($db);
        $bike = $bikeModel->find($bikeId);
        if (!$bike || !$bike['availability']) {
            $this->redirect('/');
        }
        $this->view('reserve', ['title' => 'Réserver un vélo', 'bike' => $bike, 'errors' => []]);
    }

    public function reservePost(): void
    {
        $db = Database::getInstance()->getConnection();
        $errors = [];
        $user = null;
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $bikeId = (int) ($_POST['bike_id'] ?? 0);
        $start = $_POST['start_date'] ?? '';
        $end = $_POST['end_date'] ?? '';

        if ($firstname === '' || $lastname === '' || !preg_match('/^\d{4,12}$/', $login)) {
            $errors[] = 'Renseignez vos noms et un identifiant numérique (4 à 12 chiffres).';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        if (!$start || !$end) {
            $errors[] = 'Sélectionnez les dates de début et de fin.';
        }

        $bikeModel = new Bike($db);
        $bike = $bikeModel->find($bikeId);
        if (!$bike || !$bike['availability']) {
            $errors[] = 'Ce vélo n\'est plus disponible.';
        }

        if (!$errors) {
            $userModel = new User($db);
            $user = $userModel->findByLogin((int) $login);
            if (!$user) {
                $userModel->create([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'login' => $login,
                    'password' => $password,
                    'role' => 'citizen',
                ]);
                $user = $userModel->findByLogin((int) $login);
            } elseif (!password_verify($password, $user['password'])) {
                $errors[] = 'Cet identifiant appartient déjà à un compte. Vérifiez le mot de passe.';
            }
        }

        if (!$errors) {
            try {
                $reservationNum = (new Reservation($db))->create([
                    'user_id' => $user['user_id'],
                    'bike_id' => $bikeId,
                    'start_date' => str_replace('T', ' ', $start),
                    'end_date' => str_replace('T', ' ', $end),
                ]);
                $this->view('confirmation', [
                    'title' => 'Réservation confirmée',
                    'reservationNum' => $reservationNum,
                ]);
                return;
            } catch (InvalidArgumentException $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        $this->view('reserve', [
            'title' => 'Réserver un vélo',
            'bike' => $bike,
            'errors' => $errors,
        ]);
    }
}
