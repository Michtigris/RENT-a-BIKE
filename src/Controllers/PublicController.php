<?php

class PublicController extends Controller
{
    public function home(): void
    {
        $bike = new Bike();
        $this->view('home', ['title' => 'Vélos disponibles', 'bikes' => $bike->all(true)]);
    }

    public function reserve(): void
    {
        $bike = (new Bike())->find((int) filter_input(INPUT_GET, 'bike', FILTER_VALIDATE_INT));
        if (!$bike || !$bike['availability']) {
            $this->redirect('/', 'Ce vélo n’est plus disponible.');
        }
        $this->view('reserve', ['title' => 'Réserver un vélo', 'bike' => $bike, 'errors' => [], 'fields' => []]);
    }

    public function reservePost(): void
    {
        $fields = [];
        foreach (['firstname', 'lastname', 'login', 'start_date', 'end_date'] as $name) {
            $fields[$name] = is_string($_POST[$name] ?? null) ? trim($_POST[$name]) : '';
        }
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $bikeId = (int) filter_input(INPUT_POST, 'bike_id', FILTER_VALIDATE_INT);
        $bike = (new Bike())->find($bikeId);
        if (!$bike || !$bike['availability']) {
            $this->redirect('/', 'Ce vélo n’est plus disponible.');
        }

        $errors = [];
        $login = filter_var($fields['login'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1000, 'max_range' => 2147483647],
        ]);
        if ($fields['firstname'] === '' || $fields['lastname'] === ''
            || iconv_strlen($fields['firstname'], 'UTF-8') > 255
            || iconv_strlen($fields['lastname'], 'UTF-8') > 255) {
            $errors[] = 'Renseignez votre prénom et votre nom (255 caractères maximum chacun).';
        }
        if ($login === false) {
            $errors[] = 'Indiquez un identifiant numérique compris entre 1000 et 2147483647.';
        }
        if (strlen($password) < 6 || strlen($password) > 72 || strpos($password, chr(0)) !== false) {
            $errors[] = 'Indiquez un mot de passe de 6 à 72 octets.';
        }

        $start = strtotime($fields['start_date']);
        $end = strtotime($fields['end_date']);
        if ($start === false || $end === false
            || date('Y-m-d\\TH:i', $start) !== $fields['start_date']
            || date('Y-m-d\\TH:i', $end) !== $fields['end_date']) {
            $errors[] = 'Sélectionnez des dates de début et de fin valides.';
        } elseif ($end <= $start) {
            $errors[] = 'La date de fin doit être après la date de début.';
        }

        if (!$errors) {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                $userModel = new User();
                $user = $userModel->findByLogin($login);
                if (!$user) {
                    $userModel->create([
                        'firstname' => $fields['firstname'],
                        'lastname' => $fields['lastname'],
                        'login' => $login,
                        'password' => $password,
                    ]);
                    $userId = (int) $db->lastInsertId();
                } else {
                    if ($user['role'] !== 'citizen' || !password_verify($password, $user['password'])) {
                        throw new InvalidArgumentException('Cet identifiant est déjà utilisé. Vérifiez vos identifiants.');
                    }
                    $userId = $user['user_id'];
                }

                $reservationNum = (new Reservation())->create([
                    'user_id' => $userId,
                    'bike_id' => $bikeId,
                    'start_date' => date('Y-m-d H:i:s', $start),
                    'end_date' => date('Y-m-d H:i:s', $end),
                ]);
                $db->commit();
                $_SESSION['reservation_number'] = $reservationNum;
                $this->redirect('/confirmation');
            } catch (InvalidArgumentException $exception) {
                $db->rollBack();
                $errors[] = $exception->getMessage();
            } catch (PDOException $exception) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                if ($exception->getCode() !== '23000') {
                    throw $exception;
                }
                $errors[] = 'La demande n’a pas pu être enregistrée. Vérifiez vos informations puis réessayez.';
            }
        }

        $this->view('reserve', [
            'title' => 'Réserver un vélo',
            'bike' => $bike,
            'errors' => $errors,
            'fields' => $fields,
        ]);
    }

    public function confirmation(): void
    {
        if (empty($_SESSION['reservation_number'])) {
            $this->redirect('/');
        }

        $this->view('confirmation', [
            'title' => 'Demande enregistrée',
            'reservationNum' => $_SESSION['reservation_number'],
        ]);
    }
}
