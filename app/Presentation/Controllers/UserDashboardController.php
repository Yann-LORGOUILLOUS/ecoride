<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/VehiculeRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';

final class UserDashboardController extends BaseController
{
    public function userDashboard(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        $allowedModes = ['passenger', 'driver', 'both'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dashboard_mode'])) {
            $mode = (string) $_POST['dashboard_mode'];
            if (in_array($mode, $allowedModes, true)) {
                $_SESSION['dashboard_mode'] = $mode;
            }
        }

        $dashboardMode = (string) ($_SESSION['dashboard_mode'] ?? 'both');
        if (!in_array($dashboardMode, $allowedModes, true)) {
            $dashboardMode = 'both';
            $_SESSION['dashboard_mode'] = 'both';
        }

        $userId = (int) $_SESSION['user']['id'];

        $userRepo = new UserRepository();
        $vehiculeRepo = new VehiculeRepository();
        $reviewRepo = new ReviewRepository();

        $user = $userRepo->findProfileById($userId);

        if (!is_array($user)) {
            header('Location: ' . BASE_URL . '/deconnexion');
            exit;
        }

        $vehicules = $vehiculeRepo->findByUserId($userId);
        $vehiculesPreview = is_array($vehicules) ? array_slice($vehicules, 0, 2) : [];

        $rating = $reviewRepo->getDriverRatingSummary($userId);
        if (!is_array($rating)) {
            $rating = ['avg' => 0, 'count' => 0];
        }

        $this->renderView('mon-compte', [
            'pageTitle' => 'Mon Compte',
            'dashboardMode' => $dashboardMode,
            'user' => $user,
            'vehiculesPreview' => $vehiculesPreview,
            'rating' => $rating,
        ]);
    }
}
