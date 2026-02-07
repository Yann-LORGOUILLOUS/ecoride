<?php

require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ReviewRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/IncidentRepository.php';

class EmployeeDashboardController extends BaseController
{
    public function employeeDashboard()
    {
        $tripRepository = new TripRepository();
        $reviewRepository = new ReviewRepository();
        $incidentRepository = new IncidentRepository();

        $pendingTripsCount = $tripRepository->countPending();
        $pendingReviewsCount = $reviewRepository->countPending();

        $openTripIncidentsCount = 0;
        try {
            $openTripIncidentsCount = $incidentRepository->countPendingTripIncidents();
        } catch (Throwable $e) {
            $openTripIncidentsCount = 0;
        }

        $this->renderView('dashboard-moderateur', [
            'pageTitle' => 'DASHBOARD MODERATEUR',
            'pendingTripsCount' => $pendingTripsCount,
            'pendingReviewsCount' => $pendingReviewsCount,
            'openTripIncidentsCount' => $openTripIncidentsCount,
        ]);
    }
}
