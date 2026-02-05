<?php declare(strict_types=1);

require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';

final class EmployeeTripValidationController extends BaseController
{
    public function employeeTripValidation(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        if (($_SESSION['user']['role'] ?? null) !== 'employee') {
            header('Location: ' . BASE_URL . '/mon-compte');
            exit;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $tripId = filter_input(INPUT_GET, 'trip_id', FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if (!is_int($tripId)) {
            http_response_code(404);
            echo '404 - trajet introuvable';
            return;
        }

        $tripRepo = new TripRepository();
        $trip = $tripRepo->findDetailsById($tripId);

        if ($trip === null) {
            http_response_code(404);
            echo '404 - trajet introuvable';
            return;
        }

        if (($trip['status'] ?? '') !== 'pending') {
            header('Location: ' . BASE_URL . '/liste-trajets-a-valider');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = (string)($_POST['csrf_token'] ?? '');
            if (!hash_equals((string)$_SESSION['csrf_token'], $csrf)) {
                http_response_code(403);
                echo '403 - CSRF token invalide';
                return;
            }

            $action = (string)($_POST['action'] ?? '');

            if ($action === 'approve') {
                $priceCredits = filter_input(INPUT_POST, 'price_credits', FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1],
                ]);

                if (!is_int($priceCredits)) {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Le coût en crédits doit être renseigné (minimum 1) pour valider.',
                    ];
                    header('Location: ' . BASE_URL . '/valider-trajet?trip_id=' . $tripId);
                    exit;
                }

                $tripRepo->moderateTrip($tripId, 'planned', $priceCredits);

                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Trajet validé : il est maintenant publié et réservable.',
                ];

                header('Location: ' . BASE_URL . '/liste-trajets-a-valider');
                exit;
            }

            if ($action === 'reject') {
                $reason = trim((string)($_POST['reject_reason'] ?? ''));
                if ($reason === '') {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'La raison du refus est obligatoire.',
                    ];
                    header('Location: ' . BASE_URL . '/valider-trajet?trip_id=' . $tripId);
                    exit;
                }

                $tripRepo->moderateTrip($tripId, 'cancelled', null);

                $to = (string)($trip['driver_email'] ?? '');
                $driverPseudo = (string)($trip['driver_pseudo'] ?? '');
                $fromCity = (string)($trip['city_from'] ?? '');
                $toCity = (string)($trip['city_to'] ?? '');
                $depAt = (string)($trip['departure_datetime'] ?? '');

                $mailSent = false;
                if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                    $subject = 'EcoRide - Trajet refusé';
                    $body = "Bonjour {$driverPseudo},\n\n"
                        . "Votre trajet {$fromCity} → {$toCity} (départ : {$depAt}) a été refusé par la modération.\n\n"
                        . "Raison :\n{$reason}\n\n"
                        . "Vous pouvez modifier votre trajet et le reproposer.\n\n"
                        . "— EcoRide\n";

                    $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
                    $mailSent = @mail($to, $subject, $body, $headers);
                }

                $_SESSION['flash'] = [
                    'type' => $mailSent ? 'success' : 'warning',
                    'message' => $mailSent
                        ? 'Trajet refusé et email envoyé au conducteur.'
                        : "Trajet refusé. Email non envoyé (configuration mail indisponible).",
                ];

                header('Location: ' . BASE_URL . '/liste-trajets-a-valider');
                exit;
            }

            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Action invalide.',
            ];
            header('Location: ' . BASE_URL . '/valider-trajet?trip_id=' . $tripId);
            exit;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('valider-trajet', [
            'pageTitle' => 'Validation du trajet',
            'trip' => $trip,
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
    }
}
