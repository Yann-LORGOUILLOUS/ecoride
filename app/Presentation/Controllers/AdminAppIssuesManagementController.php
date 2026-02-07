<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/IncidentRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';

final class AdminAppIssuesManagementController extends BaseController
{
    public function adminAppIssuesManagement(): void
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

        $incidentRepo = new IncidentRepository();
        $userRepo = new UserRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = (string)($_POST['csrf_token'] ?? '');
            if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrf)) {
                http_response_code(403);
                echo '403 - CSRF token invalide';
                return;
            }

            $action = (string)($_POST['action'] ?? '');
            $oid = (string)($_POST['incident_oid'] ?? '');

            if ($action === 'validate' && $oid !== '') {
                $adminId = (int)($_SESSION['user']['id'] ?? 0);
                $incidentRepo->closeAppIssue($oid, $adminId, 'validated', null);

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signalement validé et clôturé.'];
                header('Location: ' . BASE_URL . '/gestion-signalements-techniques');
                exit;
            }

            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action invalide.'];
            header('Location: ' . BASE_URL . '/gestion-signalements-techniques');
            exit;
        }

        $issues = $incidentRepo->findPendingAppIssues();

        $reporters = [];
        foreach ($issues as $issue) {
            $rid = (int)($issue['reporter_user_id'] ?? 0);
            if ($rid > 0) { $reporters[$rid] = null; }
        }

        if (!empty($reporters)) {
            foreach (array_keys($reporters) as $rid) {
                $u = $userRepo->findById($rid);
                $reporters[$rid] = is_array($u) ? (string)($u['pseudo'] ?? '') : '';
            }
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('gestion-signalements-techniques', [
            'pageTitle' => 'Gestion des signalements techniques',
            'csrfToken' => $_SESSION['csrf_token'],
            'flash' => $flash,
            'issues' => $issues,
            'reporters' => $reporters,
        ]);
    }
}
