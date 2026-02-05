<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';

final class LoginController extends BaseController
{
    public function login(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $redirect = (string)($_GET['redirect'] ?? '');
        if ($redirect !== '' && $this->isSafeRedirect($redirect)) {
            $_SESSION['after_login_redirect'] = $redirect;
        }
        
        $this->renderView('connexion', [
            'pageTitle' => 'Connexion',
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        unset($_SESSION['user']);
        session_regenerate_id(true);

        header('Location: ' . BASE_URL . '/');
        exit;
    }

    private function handlePost(): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');

        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $this->flash('danger', 'Requête invalide. Merci de réessayer.');
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Email ou mot de passe invalide.');
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);

        if (!is_array($user) || (int)($user['suspended'] ?? 0) === 1) {
            $this->flash('danger', 'Identifiants incorrects.');
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        $stored = (string)($user['password_hash'] ?? '');
        if (!$this->verifyPassword($password, $stored)) {
            $this->flash('danger', 'Identifiants incorrects.');
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'pseudo' => (string)$user['pseudo'],
            'email' => (string)$user['email'],
            'role' => (string)$user['role'],
        ];

        $target = (string)($_SESSION['after_login_redirect'] ?? '');
        unset($_SESSION['after_login_redirect']);

        if ($target !== '' && $this->isSafeRedirect($target)) {
            header('Location: ' . $target);
            exit;
        }

        $role = (string)($_SESSION['user']['role'] ?? '');

        if ($role === 'employee') {
            header('Location: ' . BASE_URL . '/dashboard-moderateur');
            exit;
        }

        header('Location: ' . BASE_URL . '/mon-compte');
        exit;
    }

    private function verifyPassword(string $input, string $stored): bool
    {
        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon2')) {
            return password_verify($input, $stored);
        }

        return $stored !== '' && hash_equals($stored, $input);
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    private function isSafeRedirect(string $path): bool
    {
        if ($path === '' || $path[0] !== '/') return false;
        if (str_starts_with($path, '//')) return false;
        if (str_contains($path, '://')) return false;
        return true;
    }
}

