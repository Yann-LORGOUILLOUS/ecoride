<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';

final class SignUpController extends BaseController
{
    public function signUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $redirect = (string)($_GET['redirect'] ?? '');
        if ($redirect !== '' && $this->isSafeRedirect($redirect)) {
            $_SESSION['after_signup_redirect'] = $redirect;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('inscription', [
            'pageTitle' => 'Inscription',
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
    }

    private function handlePost(): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');

        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $this->flash('danger', 'Requête invalide. Merci de réessayer.');
            header('Location: ' . BASE_URL . '/inscription');
            exit;
        }

        $pseudo = trim((string)($_POST['pseudo'] ?? ''));
        $lastName = trim((string)($_POST['last_name'] ?? ''));
        $firstName = trim((string)($_POST['first_name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $pwdErrors = $this->validatePassword($password);
        if ($pwdErrors !== []) {
            $this->renderFormError(
                'Mot de passe trop faible : ' . implode(' ', $pwdErrors),
                $old
            );
            return;
        }
        $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

        if ($pseudo === '' || $lastName === '' || $firstName === '' || $email === '' || $password === '' || $passwordConfirm === '') {
            $this->flash('danger', 'Tous les champs sont obligatoires.');
            header('Location: ' . BASE_URL . '/inscription');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Email invalide.');
            header('Location: ' . BASE_URL . '/inscription');
            exit;
        }

        if ($password !== $passwordConfirm) {
            $this->flash('danger', 'Les mots de passe ne correspondent pas.');
            header('Location: ' . BASE_URL . '/inscription');
            exit;
        }

        if (mb_strlen($password) < 8) {
            $this->flash('danger', 'Mot de passe trop court (8 caractères minimum).');
            header('Location: ' . BASE_URL . '/inscription');
            exit;
        }

        $repo = new UserRepository();

        if ($repo->existsByEmail($email)) {
            $this->flash('danger', 'Un compte existe déjà avec cet email.');
            header('Location: ' . BASE_URL . '/inscription');
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $id = $repo->createUser([
            'pseudo' => $pseudo,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'password_hash' => $passwordHash,
            'avatar_url' => null,
            'role' => 'user',
            'credits' => 20,
            'suspended' => 0,
        ]);

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $id,
            'pseudo' => $pseudo,
            'email' => $email,
            'role' => 'user',
        ];

        $target = (string)($_SESSION['after_signup_redirect'] ?? '');
        unset($_SESSION['after_signup_redirect']);

        if ($target !== '' && $this->isSafeRedirect($target)) {
            header('Location: ' . $target);
            exit;
        }

        header('Location: ' . BASE_URL . '/mon-compte');
        exit;
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

    private function validatePassword(string $password): array
    {
        $errors = [];

        if (mb_strlen($password) < 12) {
            $errors[] = '12 caractères minimum.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Au moins une minuscule.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Au moins une majuscule.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Au moins un chiffre.';
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'Au moins un caractère spécial.';
        }

        return $errors;
    }
}
