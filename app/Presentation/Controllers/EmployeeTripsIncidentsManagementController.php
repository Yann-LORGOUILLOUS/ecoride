<?php declare(strict_types=1);

use App\Infrastructure\Mail\Mailer;

require_once __DIR__ . '/../../Infrastructure/Repositories/IncidentRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';

final class EmployeeTripsIncidentsManagementController extends BaseController
{
    public function employeeTripsIncidentsManagement(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/connexion'); exit;
        }

        if (($_SESSION['user']['role'] ?? null) !== 'employee') {
            header('Location: ' . BASE_URL . '/mon-compte'); exit;
        }

        $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

        $incidentRepo = new IncidentRepository();
        $userRepo = new UserRepository();
        $tripRepo = new TripRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = (string)($_POST['csrf_token'] ?? '');
            if (!hash_equals((string)$_SESSION['csrf_token'], $csrf)) {
                http_response_code(403); echo '403 - CSRF token invalide'; return;
            }

            $action = (string)($_POST['action'] ?? '');
            $oid = trim((string)($_POST['incident_oid'] ?? ''));

            if ($oid === '') {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Signalement introuvable.'];
                header('Location: ' . BASE_URL . '/gestion-signalements-trajets'); exit;
            }

            $employeeId = (int)($_SESSION['user']['id'] ?? 0);

            if ($action === 'approve') {
                $incidentRepo->updateByOid($oid, function (&$doc) use ($employeeId, $tripRepo, $userRepo) {
                    $status = (string)($doc['status'] ?? '');
                    if ($status === 'open') { $status = 'pending'; }
                    if ($status !== 'pending') { return; }

                    $tripId = (int)($doc['trip_id'] ?? 0);
                    $reporterId = (int)($doc['reporter_user_id'] ?? 0);

                    $targetId = (int)($doc['target_user_id'] ?? 0);
                    if ($targetId < 1 && $tripId > 0) {
                        $trip = $tripRepo->findDetailsById($tripId);
                        $targetId = (int)($trip['driver_id'] ?? 0);
                        $doc['target_user_id'] = $targetId;
                    }

                    $doc['status'] = 'approved';
                    $doc['moderation'] = $doc['moderation'] ?? [];
                    $doc['moderation']['handled_by_employee_id'] = $employeeId;
                    $doc['moderation']['handled_at'] = gmdate('c');
                    $doc['moderation']['decision'] = 'approved';
                    $doc['moderation']['decision_reason'] = null;

                    if ($targetId > 0) {
                        $userRepo->incrementValidatedReportsCount($targetId);
                        $target = $userRepo->findById($targetId);
                        $to = is_array($target) ? (string)($target['email'] ?? '') : '';
                        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                            $subject = 'EcoRide - Signalement validé';
                            $body = "Bonjour,\n\n"
                                . "Un signalement vous concernant a été validé par la modération.\n"
                                . "Merci de veiller au respect des règles EcoRide.\n\n"
                                . "— EcoRide\n";
                            Mailer::send($to, $subject, $body);
                        }
                    }
                });

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signalement validé.'];
                header('Location: ' . BASE_URL . '/gestion-signalements-trajets'); exit;
            }

            // REJECT
            if ($action === 'reject') {
                $reason = trim((string)($_POST['reject_reason'] ?? ''));
                if ($reason === '') {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'La raison du refus est obligatoire.'];
                    header('Location: ' . BASE_URL . '/gestion-signalements-trajets'); exit;
                }

                $incidentRepo->updateByOid($oid, function (&$doc) use ($employeeId, $userRepo, $reason) {
                    $status = (string)($doc['status'] ?? '');
                    if ($status === 'open') { $status = 'pending'; }
                    if ($status !== 'pending') { return; }

                    $doc['status'] = 'rejected';
                    $doc['moderation'] = $doc['moderation'] ?? [];
                    $doc['moderation']['handled_by_employee_id'] = $employeeId;
                    $doc['moderation']['handled_at'] = gmdate('c');
                    $doc['moderation']['decision'] = 'rejected';
                    $doc['moderation']['decision_reason'] = $reason;
                    $reporterId = (int)($doc['reporter_user_id'] ?? 0);
                    if ($reporterId > 0) {
                        $reporter = $userRepo->findById($reporterId);
                        $to = is_array($reporter) ? (string)($reporter['email'] ?? '') : '';
                        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                            $subject = 'EcoRide - Signalement refusé';
                            $body = "Bonjour,\n\n"
                                . "Votre signalement a été refusé par la modération.\n\n"
                                . "Raison :\n{$reason}\n\n"
                                . "— EcoRide\n";
                            Mailer::send($to, $subject, $body);
                        }
                    }
                });

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signalement refusé.'];
                header('Location: ' . BASE_URL . '/gestion-signalements-trajets'); exit;
            }

            if ($action === 'escalate') {
                $incidentRepo->updateByOid($oid, function (&$doc) use ($employeeId) {
                    $status = (string)($doc['status'] ?? '');
                    if ($status === 'open') { $status = 'pending'; }
                    if ($status !== 'pending') { return; }

                    $doc['status'] = 'escalated';
                    $doc['moderation'] = $doc['moderation'] ?? [];
                    $doc['moderation']['handled_by_employee_id'] = $employeeId;
                    $doc['moderation']['handled_at'] = gmdate('c');
                    $doc['moderation']['decision'] = 'escalated';
                    $doc['moderation']['decision_reason'] = null;
                });

                if (defined('MAIL_ADMIN') && filter_var(MAIL_ADMIN, FILTER_VALIDATE_EMAIL)) {
                    $subject = 'EcoRide - Signalement transféré à l’administrateur';
                    $body = "Bonjour,\n\nUn signalement a été transféré à l’administration.\nOID: {$oid}\n\n— EcoRide\n";
                    Mailer::send(MAIL_ADMIN, $subject, $body);
                }

                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signalement transféré à l’administrateur.'];
                header('Location: ' . BASE_URL . '/gestion-signalements-trajets'); exit;
            }

            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Action invalide.'];
            header('Location: ' . BASE_URL . '/gestion-signalements-trajets'); exit;
        }

        $pending = $incidentRepo->findPendingTripIncidents();

        $items = [];
        foreach ($pending as $doc) {
            $oid = $incidentRepo->getOid($doc);
            $tripId = (int)($doc['trip_id'] ?? 0);
            $reporterId = (int)($doc['reporter_user_id'] ?? 0);
            $targetId = (int)($doc['target_user_id'] ?? 0);
            $trip = $tripId > 0 ? $tripRepo->findDetailsById($tripId) : null;

            if ($targetId < 1 && is_array($trip)) {
                $targetId = (int)($trip['driver_id'] ?? 0);
            }

            $reporter = $reporterId > 0 ? $userRepo->findById($reporterId) : null;
            $target = $targetId > 0 ? $userRepo->findById($targetId) : null;
            $driverId = is_array($trip) ? (int)($trip['driver_id'] ?? 0) : 0;
            $items[] = [
                'oid' => $oid,
                'trip' => $trip,
                'comment' => (string)($doc['comment'] ?? ''),
                'reporter' => $reporter,
                'target' => $target,
                'reporter_trip_role' => ($reporterId === $driverId) ? 'CONDUCTEUR' : 'PASSAGER',
                'target_trip_role' => ($targetId === $driverId) ? 'CONDUCTEUR' : 'PASSAGER',
            ];
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('gestion-signalements-trajets', [
            'pageTitle' => 'Gestion des Signalements',
            'items' => $items,
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
    }
}
