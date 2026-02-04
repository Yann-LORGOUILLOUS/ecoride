<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReservationRepository.php';

final class ReserveTripController extends BaseController
{
    public function reserve(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $redirect = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . BASE_URL . '/connexion?redirect=' . urlencode($redirect));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo '405 - Méthode non autorisée';
            exit;
        }

        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');

        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Requête invalide. Merci de réessayer.'];
            header('Location: ' . BASE_URL . '/trajets');
            exit;
        }

        $back = (string)($_POST['back'] ?? '');
        $backParam = $back !== '' ? '&back=' . urlencode($back) : '';

        $tripId = filter_input(INPUT_POST, 'trip_id', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if (!is_int($tripId)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Trajet invalide.'];
            header('Location: ' . BASE_URL . '/trajets');
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];

        try {
            $repo = new ReservationRepository();
            $repo->reserveTrip($tripId, $userId);

            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Félicitations ! Trajet réservé.'];
            header('Location: ' . BASE_URL . '/details-trajet?id=' . $tripId . '&reserved=1' . $backParam);
            exit;
        } catch (\RuntimeException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
            header('Location: ' . BASE_URL . '/details-trajet?id=' . $tripId . $backParam);
            exit;
        }
    }
}
