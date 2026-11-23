<?php

session_start();
require_once __DIR__ . '/../config/config.php';

$directory = trim(str_replace('\\', '/', dirname(rawurldecode($_SERVER['SCRIPT_NAME']))), '/');
$basePath = $directory === '' ? '' : '/' . $directory;
define('BASE_URL', $directory === '' ? '' : '/' . implode('/', array_map('rawurlencode', explode('/', $directory))));
define('PUBLIC_URL', BASE_URL . (defined('FROM_PROJECT_ROOT') ? '/public' : ''));

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars($_SESSION['csrf']) . '">';
}

spl_autoload_register(function (string $class): void {
    foreach (['Controllers', 'Models'] as $directory) {
        $file = __DIR__ . '/../src/' . $directory . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

$routes = [
    'GET' => [
        '/' => [PublicController::class, 'home'],
        '/reserve' => [PublicController::class, 'reserve'],
        '/confirmation' => [PublicController::class, 'confirmation'],
        '/login' => [AuthController::class, 'login'],
        '/dashboard' => [DashboardController::class, 'index'],
        '/dashboard/bikes' => [DashboardController::class, 'bikes'],
        '/dashboard/users' => [DashboardController::class, 'users'],
        '/dashboard/reservations' => [DashboardController::class, 'allReservations'],
        '/dashboard/search' => [DashboardController::class, 'searchReservations'],
    ],
    'POST' => [
        '/reserve' => [PublicController::class, 'reservePost'],
        '/login' => [AuthController::class, 'authenticate'],
        '/logout' => [AuthController::class, 'logout'],
        '/dashboard/bikes' => [DashboardController::class, 'addBike'],
        '/dashboard/reservation/status' => [DashboardController::class, 'updateReservationStatus'],
        '/dashboard/reservation/delete' => [DashboardController::class, 'deleteReservation'],
        '/dashboard/bike/delete' => [DashboardController::class, 'deleteBike'],
        '/dashboard/user/delete' => [DashboardController::class, 'deleteUser'],
    ],
];

$method = $_SERVER['REQUEST_METHOD'] === 'HEAD' ? 'GET' : $_SERVER['REQUEST_METHOD'];
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($path === $basePath || str_starts_with($path, $basePath . '/')) {
    $path = substr($path, strlen($basePath));
}
$path = '/' . trim($path, '/');
if ($path === '/index.php') {
    $path = '/';
}

if (!isset($routes[$method][$path])) {
    http_response_code(404);
    exit('Page introuvable');
}

if ($method === 'POST' && (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf']))) {
    http_response_code(403);
    exit('Formulaire expiré. Rechargez la page puis réessayez.');
}

[$controller, $action] = $routes[$method][$path];
try {
    (new $controller())->$action();
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    http_response_code(503);
    echo 'Une erreur de base de données est survenue. Vérifiez MySQL dans MAMP et la configuration du projet.';
}
