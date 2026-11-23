<?php

session_start();
require_once dirname(__DIR__) . '/config.php';

$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $scriptDirectory === '/' ? '' : rtrim($scriptDirectory, '/'));

spl_autoload_register(function (string $class): void {
    $directories = [
        dirname(__DIR__) . '/app/Core/',
        dirname(__DIR__) . '/app/Controllers/',
        dirname(__DIR__) . '/app/Models/',
    ];
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

$router = new Router();
$public = new PublicController();
$auth = new AuthController();
$dashboard = new DashboardController();

$router->get('/', [$public, 'home']);
$router->get('/reserve', [$public, 'reserve']);
$router->post('/reserve', [$public, 'reservePost']);
$router->get('/login', [$auth, 'login']);
$router->post('/login', [$auth, 'authenticate']);
$router->get('/logout', [$auth, 'logout']);
$router->get('/dashboard', [$dashboard, 'index']);
$router->get('/dashboard/bikes', [$dashboard, 'bikes']);
$router->post('/dashboard/bikes', [$dashboard, 'addBike']);
$router->get('/dashboard/users', [$dashboard, 'users']);
$router->get('/dashboard/reservations', [$dashboard, 'allReservations']);
$router->post('/dashboard/reservation/status', [$dashboard, 'updateReservationStatus']);
$router->post('/dashboard/reservation/delete/{id}', [$dashboard, 'deleteReservation']);
$router->post('/dashboard/bike/delete/{id}', [$dashboard, 'deleteBike']);
$router->post('/dashboard/user/delete/{id}', [$dashboard, 'deleteUser']);
$router->get('/dashboard/search', [$dashboard, 'searchReservations']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
