<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReservationRepository.php';

final class UserInfosController extends BaseController
{
    public function userInfos(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            header('Location: ' . BASE_URL . '/connexion');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            return;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $userId = (int)$_SESSION['user']['id'];

        $repo = new UserRepository();
        $user = $repo->findEditableById($userId);
        $tripRepo = new TripRepository();
        $driverTrips = $tripRepo->findDriverActiveTrips($userId);
        $resRepo = new ReservationRepository();
        $passengerReservations = $resRepo->findConfirmedReservationsByUserId($userId);

        if (!is_array($user)) {
            $this->flash('danger', 'Utilisateur introuvable.');
            header('Location: ' . BASE_URL . '/mon-compte');
            exit;
        }

        $this->renderView('mes-informations', [
            'pageTitle' => 'Mes Informations',
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
            'user' => $user,
            'driverTrips' => $driverTrips,
            'passengerReservations' => $passengerReservations,
        ]);
    }

    private function handlePost(): void
    {
        $csrf = (string)($_POST['csrf_token'] ?? '');
        $expected = (string)($_SESSION['csrf_token'] ?? '');

        if ($expected === '' || !hash_equals($expected, $csrf)) {
            $this->flash('danger', 'Requête invalide. Merci de réessayer.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];
        $action = (string)($_POST['action'] ?? '');

        if ($action === 'update_profile') {
            $this->updateProfile($userId);
            return;
        }

        if ($action === 'update_password') {
            $this->updatePassword($userId);
            return;
        }

        if ($action === 'cancel_trip') {
            $this->cancelTrip($userId);
            return;
        }

        if ($action === 'start_trip') {
            $this->startTrip($userId);
            return;
        }

        if ($action === 'finish_trip') {
            $this->finishTrip($userId);
            return;
        }

        if ($action === 'cancel_reservation') {
            $this->cancelReservation($userId);
            return;
        }

        $this->flash('danger', 'Action invalide.');
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }

    private function updateProfile(int $userId): void
    {
        $repo = new UserRepository();

        $pseudo = trim((string)($_POST['pseudo'] ?? ''));
        $lastName = trim((string)($_POST['last_name'] ?? ''));
        $firstName = trim((string)($_POST['first_name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $avatarUrl = trim((string)($_POST['avatar_url'] ?? ''));

        if ($pseudo === '' || $lastName === '' || $firstName === '' || $email === '') {
            $this->flash('danger', 'Pseudo, nom, prénom et email sont obligatoires.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('danger', 'Email invalide.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        if ($repo->existsByEmailExceptId($email, $userId)) {
            $this->flash('danger', 'Cet email est déjà utilisé.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $repo->updateProfile($userId, [
            'pseudo' => $pseudo,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'avatar_url' => ($avatarUrl === '') ? null : $avatarUrl,
        ]);

        $_SESSION['user']['pseudo'] = $pseudo;
        $_SESSION['user']['email'] = $email;

        $this->flash('success', 'Informations mises à jour.');
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }

    private function updatePassword(int $userId): void
    {
        $repo = new UserRepository();

        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['new_password_confirm'] ?? '');

        if ($current === '' || $new === '' || $confirm === '') {
            $this->flash('danger', 'Tous les champs mot de passe sont obligatoires.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        if ($new !== $confirm) {
            $this->flash('danger', 'Les nouveaux mots de passe ne correspondent pas.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        if (strlen($new) < 8) {
            $this->flash('danger', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $hash = $repo->findPasswordHashById($userId);
        if ($hash === null || !password_verify($current, $hash)) {
            $this->flash('danger', 'Ancien mot de passe incorrect.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $repo->updatePasswordHash($userId, password_hash($new, PASSWORD_DEFAULT));

        $this->flash('success', 'Mot de passe mis à jour.');
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    private function cancelTrip(int $userId): void
    {
        $tripId = (int)($_POST['trip_id'] ?? 0);
        if ($tripId <= 0) {
            $this->flash('danger', 'Trajet invalide.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $tripRepo = new TripRepository();
        $trip = $tripRepo->findTripBasicById($tripId);

        if (!is_array($trip)) {
            $this->flash('danger', 'Trajet introuvable.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $ok = $tripRepo->cancelTripForDriver($tripId, $userId);
        if (!$ok) {
            $this->flash('danger', 'Impossible d’annuler ce trajet (statut non autorisé).');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $this->notifyPassengers($tripRepo, $tripId, 'ANNULATION', $trip);

        $this->flash('success', 'Trajet annulé. Les passagers ont été notifiés.');
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }

    private function startTrip(int $userId): void
    {
        $tripId = (int)($_POST['trip_id'] ?? 0);
        if ($tripId <= 0) {
            $this->flash('danger', 'Trajet invalide.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $tripRepo = new TripRepository();
        $trip = $tripRepo->findTripBasicById($tripId);

        if (!is_array($trip)) {
            $this->flash('danger', 'Trajet introuvable.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $ok = $tripRepo->updateStatusForDriver($tripId, $userId, 'planned', 'ongoing');
        if (!$ok) {
            $this->flash('danger', 'Impossible de démarrer ce trajet (statut non autorisé).');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $this->notifyPassengers($tripRepo, $tripId, 'DÉPART', $trip);

        $this->flash('success', 'Trajet démarré. Les passagers ont été notifiés.');
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }

    private function finishTrip(int $userId): void
    {
        $tripId = (int)($_POST['trip_id'] ?? 0);
        if ($tripId <= 0) {
            $this->flash('danger', 'Trajet invalide.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $tripRepo = new TripRepository();
        $trip = $tripRepo->findTripBasicById($tripId);

        if (!is_array($trip)) {
            $this->flash('danger', 'Trajet introuvable.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $ok = $tripRepo->updateStatusForDriver($tripId, $userId, 'ongoing', 'finished');
        if (!$ok) {
            $this->flash('danger', 'Impossible de clôturer ce trajet (statut non autorisé).');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $this->notifyPassengersForReview($tripRepo, $tripId, $trip);

        $this->flash('success', 'Trajet clôturé. Les passagers ont été invités à laisser un avis.');
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }

    private function notifyPassengers(TripRepository $tripRepo, int $tripId, string $type, array $trip): void
    {
        $contacts = $tripRepo->findPassengerContactsForTrip($tripId);

        $subject = 'EcoRide — Information sur votre covoiturage';
        $fromTo = (string)($trip['city_from'] ?? '') . ' → ' . (string)($trip['city_to'] ?? '');
        $date = (string)($trip['departure_datetime'] ?? '');

        foreach ($contacts as $c) {
            $to = (string)($c['email'] ?? '');
            if ($to === '') {
                continue;
            }

            $body = "Bonjour,\n\n"
                . "Votre covoiturage \"$fromTo\" ($date) a une mise à jour : $type.\n\n"
                . "Vous pouvez consulter votre espace EcoRide.\n\n"
                . "— EcoRide\n";

            @mail($to, $subject, $body, "Content-Type: text/plain; charset=UTF-8\r\n");
        }
    }

    private function notifyPassengersForReview(TripRepository $tripRepo, int $tripId, array $trip): void
    {
        $contacts = $tripRepo->findPassengerContactsForTrip($tripId);

        $subject = 'EcoRide — Trajet terminé : confirmez et laissez un avis';
        $fromTo = (string)($trip['city_from'] ?? '') . ' → ' . (string)($trip['city_to'] ?? '');
        $date = (string)($trip['departure_datetime'] ?? '');

        foreach ($contacts as $c) {
            $to = (string)($c['email'] ?? '');
            if ($to === '') {
                continue;
            }

            $body = "Bonjour,\n\n"
                . "Le trajet \"$fromTo\" ($date) est terminé.\n"
                . "Rendez-vous sur EcoRide pour confirmer que tout s’est bien passé et laisser un avis.\n\n"
                . "— EcoRide\n";

            @mail($to, $subject, $body, "Content-Type: text/plain; charset=UTF-8\r\n");
        }
    }

    private function cancelReservation(int $userId): void
    {
        $reservationId = (int)($_POST['reservation_id'] ?? 0);
        if ($reservationId <= 0) {
            $this->flash('danger', 'Réservation invalide.');
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $repo = new ReservationRepository();

        try {
            $result = $repo->cancelReservationWithRefund($reservationId, $userId);
        } catch (RuntimeException $e) {
            $this->flash('danger', $e->getMessage());
            header('Location: ' . BASE_URL . '/mes-informations');
            exit;
        }

        $driverEmail = (string)($result['driver']['email'] ?? '');
        $driverPseudo = (string)($result['driver']['pseudo'] ?? '');
        $trip = $result['trip'] ?? [];
        $refund = (int)($result['refund'] ?? 0);

        if ($driverEmail !== '') {
            $fromTo = (string)($trip['city_from'] ?? '') . ' → ' . (string)($trip['city_to'] ?? '');
            $date = (string)($trip['departure_datetime'] ?? '');
            $subject = 'EcoRide — Réservation annulée';
            $body =
                "Bonjour $driverPseudo,\n\n"
                . "Un passager a annulé sa réservation pour le trajet \"$fromTo\" ($date).\n"
                . "Une place vient d’être libérée.\n\n"
                . "— EcoRide\n";

            @mail($driverEmail, $subject, $body, "Content-Type: text/plain; charset=UTF-8\r\n");
        }

        $this->flash('success', "Réservation annulée. $refund crédits remboursés. Le chauffeur a été notifié.");
        header('Location: ' . BASE_URL . '/mes-informations');
        exit;
    }
}
