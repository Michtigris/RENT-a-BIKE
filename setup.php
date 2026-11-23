<?php

require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec(file_get_contents(__DIR__ . '/script.sql'));
    $pdo->exec('USE bike_rental');

    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $check->execute();
    $adminLogin = null;
    $adminPassword = null;
    if ((int) $check->fetchColumn() === 0) {
        $adminLogin = random_int(10000000, 99999999);
        $adminPassword = bin2hex(random_bytes(8));
        $stmt = $pdo->prepare(
            'INSERT INTO users (firstname, lastname, login, password, role) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            'Admin',
            'Municipal',
            $adminLogin,
            password_hash($adminPassword, PASSWORD_DEFAULT),
            'admin',
        ]);
    }

    $check = $pdo->query('SELECT COUNT(*) FROM bikes');
    if ((int) $check->fetchColumn() === 0) {
        $stmt = $pdo->prepare('INSERT INTO bikes (registration_number, availability, photo) VALUES (?, ?, ?)');
        $stmt->execute(['VELO-001', 1, null]);
        $stmt->execute(['VELO-002', 1, null]);
        $stmt->execute(['VELO-003', 1, null]);
    }

    echo 'Installation terminée.<br>';
    if ($adminLogin !== null) {
        echo 'Identifiant administrateur : <strong>' . htmlspecialchars((string) $adminLogin) . '</strong><br>';
        echo 'Mot de passe administrateur : <strong>' . htmlspecialchars($adminPassword) . '</strong><br>';
        echo 'Enregistrez ces identifiants maintenant : ils ne seront plus affichés.';
    } else {
        echo 'Un compte administrateur existe déjà ; aucun nouvel identifiant n’a été généré.';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Erreur d\'installation : ' . htmlspecialchars($e->getMessage());
}
