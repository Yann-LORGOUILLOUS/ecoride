<?php
require_once __DIR__ . '/../../Infrastructure/Repositories/TripRepository.php';

    class EmployeeTripsValidationListController extends BaseController {
        public function employeeTripsValidationList()
        {
            $tripRepository = new TripRepository();
            $pendingTrips = $tripRepository->findPendingForValidation();
            $pendingTripsCount = count($pendingTrips);

            $this->renderView('liste-trajets-a-valider', [
                'pageTitle' => 'Trajets à valider',
                'pendingTrips' => $pendingTrips,
                'pendingTripsCount' => $pendingTripsCount,
            ]);
        }
    }