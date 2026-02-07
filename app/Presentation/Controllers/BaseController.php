<?php

class BaseController
{
    protected function renderView(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/pages/' . $view . '.php';
    }

    protected function requireLogin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $current = (string)($_SERVER['REQUEST_URI'] ?? '');
            $redirect = $current !== '' ? '?redirect=' . urlencode($current) : '';
            header('Location: ' . BASE_URL . '/connexion' . $redirect);
            exit;
        }
    }

    protected function currentUserId(): int
    {
        return (int)($_SESSION['user']['id'] ?? 0);
    }
}
