<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/VehiculeRepository.php';

final class CreateRideController extends BaseController
{
    public function createRide(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $redirect = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . BASE_URL . '/connexion?redirect=' . urlencode($redirect));
            exit;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost((int)$_SESSION['user']['id']);
            return;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $vehiculesRepo = new VehiculeRepository();
        $vehicules = $vehiculesRepo->findByUserId((int)$_SESSION['user']['id']);

        $this->renderView('creer-trajet', [
            'pageTitle' => 'Proposer un Trajet',
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
            'vehicules' => $vehicules,
            'old' => [],
        ]);
    }

    private function handlePost(int $userId): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');

        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Requête invalide. Merci de réessayer.'];
            header('Location: ' . BASE_URL . '/creer-trajet');
            exit;
        }

        $cityFrom = trim((string)($_POST['city_from'] ?? ''));
        $cityTo = trim((string)($_POST['city_to'] ?? ''));
        $departure = trim((string)($_POST['departure_datetime'] ?? ''));
        $arrival = trim((string)($_POST['arrival_datetime'] ?? ''));
        $seats = filter_input(INPUT_POST, 'seats_available', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $vehiculeId = filter_input(INPUT_POST, 'vehicule_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        $smokingAllowed = isset($_POST['smoking_allowed']) ? 1 : 0;
        $petsAllowed = isset($_POST['pets_allowed']) ? 1 : 0;
        $driverNotes = trim((string)($_POST['driver_notes'] ?? ''));
        $driverNotes = $driverNotes === '' ? null : $driverNotes;
        $priceCredits = filter_input(INPUT_POST, 'price_credits', FILTER_VALIDATE_INT, ['options' => ['min_range' => 3]]);

        $old = [
            'city_from' => $cityFrom,
            'city_to' => $cityTo,
            'departure_datetime' => $departure,
            'arrival_datetime' => $arrival,
            'seats_available' => is_int($seats) ? $seats : '',
            'vehicule_id' => is_int($vehiculeId) ? $vehiculeId : '',
            'smoking_allowed' => $smokingAllowed,
            'pets_allowed' => $petsAllowed,
            'driver_notes' => $driverNotes ?? '',
            'price_credits' => is_int($priceCredits) ? $priceCredits : '',
        ];

        if ($cityFrom === '' || $cityTo === '' || !is_int($vehiculeId) || !is_int($seats) || !is_int($priceCredits)) {
            $this->renderFormError('Merci de remplir tous les champs obligatoires.', $userId, $old);
            return;
        }

        $vehRepo = new VehiculeRepository();
        $veh = $vehRepo->findOwnedById($vehiculeId, $userId);
        if ($veh === null) {
            $this->renderFormError('Véhicule invalide.', $userId, $old);
            return;
        }

        $depTs = strtotime($departure);
        $arrTs = strtotime($arrival);
        if ($depTs === false || $arrTs === false || $arrTs <= $depTs) {
            $this->renderFormError('Dates invalides : l’arrivée doit être après le départ.', $userId, $old);
            return;
        }

        $maxSeats = (int)$veh['seats_total'];
        $maxAvailableSeats = $maxSeats - 1;

        if ($maxAvailableSeats < 1) {
            $this->renderFormError(
                "Ce véhicule ne permet pas de proposer un trajet.",
                $userId,
                $old
            );
            return;
        }

        if ($seats > $maxAvailableSeats) {
            $this->renderFormError(
                "Nombre de places invalide : maximum {$maxAvailableSeats} place(s) pour ce véhicule.",
                $userId,
                $old
            );
            return;
        }

        $tripRepo = new TripRepository();
        $tripId = $tripRepo->createTrip([
            'driver_id' => $userId,
            'vehicule_id' => $vehiculeId,
            'city_from' => $cityFrom,
            'city_to' => $cityTo,
            'departure_datetime' => date('Y-m-d H:i:s', $depTs),
            'arrival_datetime' => date('Y-m-d H:i:s', $arrTs),
            'price_credits' => $priceCredits,
            'seats_available' => $seats,
            'smoking_allowed' => $smokingAllowed,
            'pets_allowed' => $petsAllowed,
            'driver_notes' => $driverNotes,
            'status' => 'pending',
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Trajet créé ! Il apparaîtra dans la liste selon le statut de validation.'];
        header('Location: ' . BASE_URL . '/details-trajet?id=' . $tripId);
        exit;
    }

    private function renderFormError(string $message, int $userId, array $old): void
    {
        $vehiculesRepo = new VehiculeRepository();
        $vehicules = $vehiculesRepo->findByUserId($userId);

        $this->renderView('creer-trajet', [
            'pageTitle' => 'Proposer un Trajet',
            'flash' => ['type' => 'danger', 'message' => $message],
            'csrfToken' => $_SESSION['csrf_token'],
            'vehicules' => $vehicules,
            'old' => $old,
        ]);
    }
}
