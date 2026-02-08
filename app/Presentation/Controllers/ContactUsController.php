<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ContactMessageRepository.php';

class ContactUsController extends BaseController
{
    public function contactUs(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('contact', [
            'pageTitle' => 'Contact',
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
    }

    private function handlePost(): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');

        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Requête invalide. Merci de réessayer.'];
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Merci de compléter tous les champs.'];
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Adresse email invalide.'];
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        if (mb_strlen($message) > 4000) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Message trop long (max 4000 caractères).'];
            header('Location: ' . BASE_URL . '/contact');
            exit;
        }

        $repo = new ContactMessageRepository();
        $repo->create($name, $email, $subject, $message);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Merci ! Votre message a bien été envoyé.'];
        header('Location: ' . BASE_URL . '/contact');
        exit;
    }
}
