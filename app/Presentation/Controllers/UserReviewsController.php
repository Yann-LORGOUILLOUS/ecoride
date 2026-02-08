<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';

final class UserReviewsController extends BaseController
{
    public function userReviews(): void
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

        $repo = new ReviewRepository();
        $rating = $repo->getDriverRatingSummary($userId);
        $reviews = $repo->findApprovedByDriverId($userId, 20);

        $this->renderView('mes-avis', [
            'pageTitle' => 'Mes avis',
            'rating' => $rating,
            'reviews' => $reviews,
        ]);
    }
}
