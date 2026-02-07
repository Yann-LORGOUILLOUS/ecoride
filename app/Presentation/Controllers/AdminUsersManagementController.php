<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';

final class AdminUsersManagementController extends BaseController
{
    public function adminUsersManagement(): void
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

        $repo = new UserRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($repo);
            return;
        }

        $q = (string)($_GET['q'] ?? '');
        $sort = (string)($_GET['sort'] ?? 'pseudo');
        $dir = (string)($_GET['dir'] ?? 'asc');

        $users = $repo->searchUsers($q, $sort, $dir);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('gestion-comptes-utilisateurs', [
            'pageTitle' => 'Gestion des Comptes',
            'csrfToken' => $_SESSION['csrf_token'],
            'flash' => $flash,
            'users' => $users,
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    private function handlePost(UserRepository $repo): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrf)) {
            http_response_code(403);
            echo '403 - CSRF token invalide';
            return;
        }

        $action = (string)($_POST['action'] ?? '');

        if ($action === 'toggle_suspend') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $next = (int)($_POST['next_suspended'] ?? 0);

            if ($userId < 1) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Utilisateur introuvable.'];
                header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
                exit;
            }

            $repo->setSuspended($userId, $next === 1);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Statut utilisateur mis à jour.'];

            header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
            exit;
        }

        if ($action === 'create_user') {
            $pseudo = trim((string)($_POST['pseudo'] ?? ''));
            $lastName = trim((string)($_POST['last_name'] ?? ''));
            $firstName = trim((string)($_POST['first_name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $role = (string)($_POST['role'] ?? 'user');
            $password = (string)($_POST['password'] ?? '');

            if ($pseudo === '' || $lastName === '' || $firstName === '' || $email === '' || $password === '') {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tous les champs sont obligatoires.'];
                header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email invalide.'];
                header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
                exit;
            }

            if (!in_array($role, ['user', 'employee'], true)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Rôle invalide.'];
                header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
                exit;
            }

            if ($repo->existsByEmail($email)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Un compte existe déjà avec cet email.'];
                header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $repo->createUser([
                'pseudo' => $pseudo,
                'last_name' => $lastName,
                'first_name' => $firstName,
                'email' => $email,
                'password_hash' => $passwordHash,
                'avatar_url' => null,
                'role' => $role,
                'credits' => 20,
                'suspended' => 0,
            ]);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Compte créé avec succès.'];
            header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
            exit;
        }

        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action inconnue.'];
        header('Location: ' . BASE_URL . '/gestion-comptes-utilisateurs');
        exit;
    }
}
