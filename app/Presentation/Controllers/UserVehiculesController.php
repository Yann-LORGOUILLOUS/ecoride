<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/VehiculeRepository.php';

final class UserVehiculesController extends BaseController
{
    public function userVehicules(): void
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

        $userId = (int)$_SESSION['user']['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($userId);
            return;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $repo = new VehiculeRepository();
        $vehicules = $repo->findByUserId($userId);

        $this->renderView('mes-vehicules', [
            'pageTitle' => 'Mes véhicules',
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
            'vehicules' => $vehicules,
            'edit' => null,
            'old' => [],
            'redirect' => (string)($_GET['redirect'] ?? ''),
        ]);
    }

    private function handlePost(int $userId): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');
        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Requête invalide. Merci de réessayer.'];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $action = (string)($_POST['action'] ?? '');
        $redirect = trim((string)($_POST['redirect'] ?? ''));

        if ($action === 'create') {
            $this->create($userId, $redirect);
            return;
        }

        if ($action === 'update') {
            $this->update($userId, $redirect);
            return;
        }

        if ($action === 'delete') {
            $this->delete($userId, $redirect);
            return;
        }

        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action inconnue.'];
        header('Location: ' . BASE_URL . '/mes-vehicules');
        exit;
    }

    private function normalizePayload(): array
    {
        $license = strtoupper(trim((string)($_POST['license_plate'] ?? '')));
        $firstReg = trim((string)($_POST['first_registration_date'] ?? ''));
        $brand = trim((string)($_POST['brand'] ?? ''));
        $model = trim((string)($_POST['model'] ?? ''));
        $color = trim((string)($_POST['color'] ?? ''));
        $energy = (string)($_POST['energy_type'] ?? '');
        $seatsAvail = filter_input(INPUT_POST, 'seats_available_default', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $seatsTotal = is_int($seatsAvail) ? $seatsAvail + 1 : null;

        return [
            'license_plate' => $license,
            'first_registration_date' => $firstReg,
            'brand' => $brand,
            'model' => $model,
            'color' => $color,
            'energy_type' => $energy,
            'seats_available_default' => is_int($seatsAvail) ? $seatsAvail : null,
            'seats_total' => is_int($seatsAvail) ? $seatsTotal : null,
        ];
    }

    private function validate(array $data): ?string
    {
        if ($data['license_plate'] === '' || $data['first_registration_date'] === '' || $data['brand'] === '' || $data['model'] === '' || $data['color'] === '') {
            return 'Merci de remplir tous les champs obligatoires.';
        }

        if (!preg_match('/^[A-Z0-9\- ]{4,20}$/', $data['license_plate'])) {
            return 'Plaque invalide (caractères autorisés : A-Z, 0-9, espace, tiret).';
        }

        $ts = strtotime($data['first_registration_date']);
        if ($ts === false) {
            return 'Date de première immatriculation invalide.';
        }

        if (!in_array($data['energy_type'], ['electric', 'hybrid', 'fuel'], true)) {
            return 'Type d’énergie invalide.';
        }

        if (!is_int($data['seats_available_default']) || $data['seats_available_default'] < 1 || $data['seats_available_default'] > 8) {
            return 'Nombre de places passagers invalide.';
        }

        return null;
    }

    private function create(int $userId, string $redirect): void
    {
        $repo = new VehiculeRepository();
        $data = $this->normalizePayload();
        $error = $this->validate($data);

        if ($error !== null) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $error];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $newId = $repo->create($userId, [
            'license_plate' => $data['license_plate'],
            'first_registration_date' => $data['first_registration_date'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'color' => $data['color'],
            'energy_type' => $data['energy_type'],
            'seats_total' => $data['seats_total'],
            'seats_available_default' => $data['seats_available_default'],
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Véhicule ajouté.'];

        if ($redirect !== '') {
            $sep = str_contains($redirect, '?') ? '&' : '?';
            header('Location: ' . $redirect . $sep . 'vehiculeId=' . $newId);
            exit;
        }

        header('Location: ' . BASE_URL . '/mes-vehicules');
        exit;
    }

    private function update(int $userId, string $redirect): void
    {
        $vehiculeId = (int)($_POST['vehicule_id'] ?? 0);
        if ($vehiculeId <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Véhicule invalide.'];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $repo = new VehiculeRepository();
        if ($repo->findOwnedById($vehiculeId, $userId) === null) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action non autorisée.'];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $data = $this->normalizePayload();
        $error = $this->validate($data);

        if ($error !== null) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $error];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $repo->updateOwned($vehiculeId, $userId, [
            'license_plate' => $data['license_plate'],
            'first_registration_date' => $data['first_registration_date'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'color' => $data['color'],
            'energy_type' => $data['energy_type'],
            'seats_total' => $data['seats_total'],
            'seats_available_default' => $data['seats_available_default'],
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Véhicule mis à jour.'];
        header('Location: ' . ($redirect !== '' ? $redirect : (BASE_URL . '/mes-vehicules')));
        exit;
    }

    private function delete(int $userId, string $redirect): void
    {
        $vehiculeId = (int)($_POST['vehicule_id'] ?? 0);
        if ($vehiculeId <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Véhicule invalide.'];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $repo = new VehiculeRepository();
        if ($repo->findOwnedById($vehiculeId, $userId) === null) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action non autorisée.'];
            header('Location: ' . BASE_URL . '/mes-vehicules');
            exit;
        }

        $repo->deleteOwned($vehiculeId, $userId);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Véhicule supprimé.'];
        header('Location: ' . ($redirect !== '' ? $redirect : (BASE_URL . '/mes-vehicules')));
        exit;
    }
}
