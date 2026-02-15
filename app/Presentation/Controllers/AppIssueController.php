<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/IncidentRepository.php';

final class AppIssueController extends BaseController
{
    public function appIssue(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('signaler-probleme-technique', [
            'pageTitle' => 'Signaler un Problème Technique',
            'csrfToken' => (string)$_SESSION['csrf_token'],
            'flash' => $flash,
        ]);
    }

    private function handlePost(): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrf)) {
            http_response_code(403);
            echo '403 - CSRF token invalide';
            return;
        }

        $page = trim((string)($_POST['page'] ?? ''));
        $subject = trim((string)($_POST['subject'] ?? ''));
        $severity = trim((string)($_POST['severity'] ?? ''));
        $comment = trim((string)($_POST['comment'] ?? ''));

        if ($page === '' || $subject === '' || $severity === '' || $comment === '') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Merci de remplir tous les champs obligatoires.'];
            header('Location: ' . BASE_URL . '/signaler-probleme-technique');
            exit;
        }

        $reporterId = (int)($_SESSION['user']['id'] ?? 0);

        try {
            $repo = new IncidentRepository();
            $repo->createAppIssue($reporterId, $page, $subject, $severity, $comment);
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Impossible d’envoyer le signalement : ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '/signaler-probleme-technique');
            exit;
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Merci ! Votre signalement a bien été envoyé.'];
        header('Location: ' . BASE_URL . '/signaler-probleme-technique');
        exit;
    }
}
