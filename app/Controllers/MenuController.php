<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Menu;

final class MenuController extends Controller
{
    private Menu $menus;

    public function __construct()
    {
        $databaseConfig = require dirname(__DIR__, 2)
            . '/config/database.php';

        $pdo = Database::connect(
            $databaseConfig['mysql']
        );

        $this->menus = new Menu($pdo);
    }

    public function index(): void
    {
        $this->render('menus/index', [
            'title' => 'Nos menus',
            'menus' => $this->menus->findVisible(),
        ]);
    }

    public function show(): void
    {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if (!is_int($id)) {
            $this->notFound();

            return;
        }

        $menu = $this->menus->findVisibleById($id);

        if ($menu === null) {
            $this->notFound();

            return;
        }

        $this->render('menus/show', [
            'title' => $menu['nom'],
            'menu' => $menu,
        ]);
    }

    private function notFound(): void
    {
        http_response_code(404);

        $this->render('errors/404');
    }
}