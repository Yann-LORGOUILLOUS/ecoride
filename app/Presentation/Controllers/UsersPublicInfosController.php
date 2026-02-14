<?php declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReservationRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';

final class UsersPublicInfosController extends BaseController
{
    public function usersPublicInfos(): void
    {
        $userRepo = new UserRepository();

        $q = trim((string)($_GET['q'] ?? ''));
        $userId = (int)($_GET['userId'] ?? ($_GET['id'] ?? 0));

        $searchResults = [];
        $profile = null;
        $driverTrips = [];
        $passengerTrips = [];
        $reviews = [];
        $rating = ['avg' => 0.0, 'count' => 0];

        if ($q !== '' && $userId <= 0) {
            $searchResults = $userRepo->searchPublicByPseudo($q);
        }

        if ($userId > 0) {
            $profile = $userRepo->findPublicById($userId);

            if (is_array($profile)) {
                $tripRepo = new TripRepository();
                $resRepo = new ReservationRepository();
                $reviewRepo = new ReviewRepository();

                $driverTrips = $tripRepo->findFinishedByDriverId($userId, 10);
                $passengerTrips = $resRepo->findFinishedTripsAsPassenger($userId, 10);

                $rating = $reviewRepo->getDriverRatingSummary($userId);
                $reviews = $reviewRepo->findApprovedByDriverId($userId, 8);
            }
        }

        $this->renderView('profils', [
            'pageTitle' => 'Profils',
            'q' => $q,
            'searchResults' => $searchResults,
            'profile' => $profile,
            'driverTrips' => $driverTrips,
            'passengerTrips' => $passengerTrips,
            'rating' => $rating,
            'reviews' => $reviews,
        ]);
    }
}
