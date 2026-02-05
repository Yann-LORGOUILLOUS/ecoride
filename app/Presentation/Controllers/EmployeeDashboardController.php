<?php
    class EmployeeDashboardController extends BaseController {
        public function employeeDashboard()
        {
            $tripRepository = new TripRepository();
            $reviewRepository = new ReviewRepository();
            $pendingTripsCount = $tripRepository->countPending();
            $pendingReviewsCount = $reviewRepository->countPending();
            $openTripIncidentsCount = 0;
            $jsonPath = __DIR__ . '/../../../database/mongodb/ecoride.reports.json';

            if (file_exists($jsonPath)) {
                $raw = file_get_contents($jsonPath);
                $data = json_decode($raw, true);

                if (is_array($data)) {
                    foreach ($data as $report) {
                        $type = $report['type'] ?? null;
                        $status = $report['status'] ?? null;

                        if ($type === 'trip_incident' && $status === 'pending') {
                            $openTripIncidentsCount++;
                        }
                    }
                }
            }

            $this->renderView('dashboard-moderateur', [
                'pageTitle' => 'DASHBOARD MODERATEUR',
                'pendingTripsCount' => $pendingTripsCount,
                'pendingReviewsCount' => $pendingReviewsCount,
                'openTripIncidentsCount' => $openTripIncidentsCount,
            ]);
        }

    }