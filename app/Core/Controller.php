<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($viewPath)) {
            throw new \RuntimeException(
                sprintf('La vue "%s" est introuvable.', $view)
            );
        }

        extract($data, EXTR_SKIP);

        require $viewPath;
    }
}