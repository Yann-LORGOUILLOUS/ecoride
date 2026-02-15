<?php declare(strict_types=1);

use App\Infrastructure\Mail\Mailer;

require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';

final class EmployeeReviewsValidationController extends BaseController
{
    public function employeeReviewsValidation(): void
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

        $reviewRepo = new ReviewRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = (string)($_POST['csrf_token'] ?? '');
            if (!hash_equals((string)$_SESSION['csrf_token'], $csrf)) {
                http_response_code(403);
                echo '403 - CSRF token invalide';
                return;
            }

            $action = (string)($_POST['action'] ?? '');
            $reviewId = filter_input(INPUT_POST, 'review_id', FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if (!is_int($reviewId)) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Avis introuvable.'];
                header('Location: ' . BASE_URL . '/valider-avis');
                exit;
            }

            $validatorId = (int)($_SESSION['user']['id'] ?? 0);

            if ($action === 'approve') {
                $reviewRepo->approve($reviewId, $validatorId);
                $_SESSION['flash'] = ['type' => 'success', 'message' => "Avis validé."];
                header('Location: ' . BASE_URL . '/valider-avis');
                exit;
            }

            if ($action === 'reject') {
                $reason = trim((string)($_POST['reject_reason'] ?? ''));
                if ($reason === '') {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => "La raison du rejet est obligatoire."];
                    header('Location: ' . BASE_URL . '/valider-avis');
                    exit;
                }

                $pending = $reviewRepo->findPendingForValidation();
                $row = null;
                foreach ($pending as $r) {
                    if ((int)$r['id'] === $reviewId) { $row = $r; break; }
                }

                $reviewRepo->reject($reviewId, $validatorId);

                $mailSent = false;
                if (is_array($row)) {
                    $to = (string)($row['author_email'] ?? '');
                    $authorPseudo = (string)($row['author_pseudo'] ?? '');
                    $tripId = (int)($row['trip_id'] ?? 0);

                    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                        $subject = 'EcoRide - Avis rejeté';
                        $body = "Bonjour {$authorPseudo},\n\n"
                            . "Votre avis (trajet #{$tripId}) a été rejeté par la modération.\n\n"
                            . "Raison :\n{$reason}\n\n"
                            . "— EcoRide\n";

                        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
                        $mailSent = Mailer::send($to, $subject, $body, $headers);
                    }
                }

                $_SESSION['flash'] = [
                    'type' => $mailSent ? 'success' : 'warning',
                    'message' => $mailSent
                        ? "Avis rejeté et email envoyé à l'auteur."
                        : "Avis rejeté. Email non envoyé (configuration mail indisponible).",
                ];

                header('Location: ' . BASE_URL . '/valider-avis');
                exit;
            }

            $_SESSION['flash'] = ['type' => 'danger', 'message' => "Action invalide."];
            header('Location: ' . BASE_URL . '/valider-avis');
            exit;
        }

        $pendingReviews = $reviewRepo->findPendingForValidation();
        $pendingReviewsCount = count($pendingReviews);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->renderView('valider-avis', [
            'pageTitle' => 'Validation des avis',
            'pendingReviews' => $pendingReviews,
            'pendingReviewsCount' => $pendingReviewsCount,
            'flash' => $flash,
            'csrfToken' => $_SESSION['csrf_token'],
        ]);
    }
}
