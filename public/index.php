<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Router;
use App\Core\Session;
use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);

require $rootPath . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable($rootPath);
$dotenv->load();

$dotenv->required([
    'APP_ENV',
    'APP_URL',
    'DB_HOST',
    'DB_PORT',
    'DB_NAME',
    'DB_USER',
    'DB_PASS',
    'SESSION_NAME',
    'SESSION_LIFETIME',
])->notEmpty();

$appConfig = require $rootPath . '/config/app.php';

Session::start($appConfig['session']);
Flash::startRequest();

$router = new Router();

require $rootPath . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);