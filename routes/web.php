<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\MenuController;
use App\Core\Router;

/** @var Router $router */

// Accueil
$router->get('/', [HomeController::class, 'index']);

// Authentification
$router->get('/inscription', [AuthController::class, 'showRegister']);
$router->post('/inscription', [AuthController::class, 'register']);

$router->get('/connexion', [AuthController::class, 'showLogin']);
$router->post('/connexion', [AuthController::class, 'login']);

$router->post('/deconnexion', [AuthController::class, 'logout']);

// Menus
$router->get('/menus', [MenuController::class, 'index']);
$router->get('/menu', [MenuController::class, 'show']);