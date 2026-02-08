<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';

final class UserCreditsController extends BaseController
{
    public function userCredits(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $redirect = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . BASE_URL . '/connexion?redirect=' . urlencode($redirect));
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];

        $userRepo = new UserRepository();
        $profile = $userRepo->findProfileById($userId);

        if (!is_array($profile)) {
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        $credits = (int)($profile['credits'] ?? 0);

        $this->renderView('mes-credits', [
            'pageTitle' => 'Mes crédits',
            'credits' => $credits,
        ]);
    }
}
