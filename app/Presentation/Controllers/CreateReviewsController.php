<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReservationRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';

final class CreateReviewsController extends BaseController
{
    public function createReviews(): void
    {
        $this->requireLogin();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $userId = $this->currentUserId();

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $reservationRepo = new ReservationRepository();
        $reviewRepo = new ReviewRepository();

        $finishedTrips = $reservationRepo->findFinishedTripsAsPassenger($userId, 100);

        $eligibleTrips = [];
        foreach ($finishedTrips as $t) {
            $tripId = (int)($t['trip_id'] ?? 0);
            if ($tripId < 1) {
                continue;
            }
            if ($reviewRepo->existsForTripAndAuthor($tripId, $userId)) {
                continue;
            }
            $eligibleTrips[] = $t;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($userId, $eligibleTrips);
            return;
        }

        $this->renderView('rediger-avis', [
            'pageTitle' => 'Rédiger un Avis',
            'flash' => $flash,
            'csrfToken' => (string)$_SESSION['csrf_token'],
            'trips' => $eligibleTrips,
            'old' => [],
        ]);
    }

    private function handlePost(int $userId, array $eligibleTrips): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $csrf)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Requête invalide. Merci de réessayer.'];
            header('Location: ' . BASE_URL . '/rediger-avis');
            exit;
        }

        $tripId = filter_input(INPUT_POST, 'trip_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        $comment = trim((string)($_POST['comment'] ?? ''));

        $old = [
            'trip_id' => is_int($tripId) ? $tripId : '',
            'rating' => is_int($rating) ? $rating : '',
            'comment' => $comment,
        ];

        if (!is_int($tripId) || !is_int($rating) || $comment === '') {
            $this->renderFormError('Merci de remplir tous les champs.', $eligibleTrips, $old);
            return;
        }

        $isEligible = false;
        foreach ($eligibleTrips as $t) {
            if ((int)($t['trip_id'] ?? 0) === $tripId) {
                $isEligible = true;
                break;
            }
        }

        if (!$isEligible) {
            $this->renderFormError("Vous ne pouvez pas déposer d'avis pour ce trajet.", $eligibleTrips, $old);
            return;
        }

        $reviewRepo = new ReviewRepository();

        if ($reviewRepo->existsForTripAndAuthor($tripId, $userId)) {
            $this->renderFormError("Vous avez déjà déposé un avis pour ce trajet.", $eligibleTrips, $old);
            return;
        }

        try {
            $reviewRepo->createPending($tripId, $userId, $rating, $comment);
        } catch (\Throwable $e) {
            $this->renderFormError($e->getMessage(), $eligibleTrips, $old);
            return;
        }

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => "Avis envoyé ! Il sera publié après validation par la modération.",
        ];
        header('Location: ' . BASE_URL . '/rediger-avis');
        exit;
    }

    private function renderFormError(string $message, array $eligibleTrips, array $old): void
    {
        $this->renderView('rediger-avis', [
            'pageTitle' => 'Rédiger un Avis',
            'flash' => ['type' => 'danger', 'message' => $message],
            'csrfToken' => (string)($_SESSION['csrf_token'] ?? ''),
            'trips' => $eligibleTrips,
            'old' => $old,
        ]);
    }
}
