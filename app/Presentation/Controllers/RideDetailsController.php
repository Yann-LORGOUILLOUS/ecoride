<?php declare(strict_types=1);

require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';

class RideDetailsController extends BaseController
{
    public function rideDetails(): void
    {
        $tripId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
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

        $driverId = (int)($trip['driver_id'] ?? 0);
        $reviewRepo = new ReviewRepository();
        $rating = $driverId > 0 ? $reviewRepo->getDriverRatingSummary($driverId) : ['avg' => 0.0, 'count' => 0];
        $reviews = $driverId > 0 ? $reviewRepo->findApprovedByDriverId($driverId, 8) : [];

        $this->renderView('details-trajet', [
            'pageTitle' => 'Détails du trajet',
            'trip' => $trip,
            'driverRating' => $rating,
            'driverReviews' => $reviews,
        ]);
    }
}
