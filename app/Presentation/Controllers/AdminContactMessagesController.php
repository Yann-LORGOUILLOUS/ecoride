<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ContactMessageRepository.php';

final class AdminContactMessagesController extends BaseController
{
    public function adminContactMessages(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        if (($_SESSION['user']['role'] ?? null) !== 'admin') {
            header('Location: ' . BASE_URL . '/mon-compte');
            exit;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $repo = new ContactMessageRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($repo);
            return;
        }

        $messages = $repo->findAll(100, 0);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('gestion-messages-contact', [
            'pageTitle' => 'Messages de contact',
            'csrfToken' => $_SESSION['csrf_token'],
            'flash' => $flash,
            'messages' => $messages,
        ]);
    }

    private function handlePost(ContactMessageRepository $repo): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrf)) {
            http_response_code(403);
            echo '403 - CSRF token invalide';
            return;
        }

        $action = (string)($_POST['action'] ?? '');
        $id = (int)($_POST['message_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Message invalide.'];
            header('Location: ' . BASE_URL . '/gestion-messages-contact');
            exit;
        }

        if ($action === 'mark_read') {
            $repo->updateStatus($id, 'read');
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Message marqué comme lu.'];
        } else if ($action === 'archive') {
            $repo->updateStatus($id, 'archived');
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Message archivé.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action inconnue.'];
        }

        header('Location: ' . BASE_URL . '/gestion-messages-contact');
        exit;
    }
}
