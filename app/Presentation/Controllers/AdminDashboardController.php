<?php

require_once __DIR__ . '/../../Infrastructure/Repositories/UserRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/IncidentRepository.php';
require_once __DIR__ . '/../../Infrastructure/Repositories/ContactMessageRepository.php';

class AdminDashboardController extends BaseController
{
    public function adminDashboard()
    {
        $userRepository = new UserRepository();
        $incidentRepository = new IncidentRepository();
        $usersCount = $userRepository->countAll();
        $pendingTechnicalReportsCount = 0;
        try {
            $pendingTechnicalReportsCount = $incidentRepository->countPendingTechnicalReports();
        } catch (Throwable $e) {
            $pendingTechnicalReportsCount = 0;
        }
        $contactRepo = new ContactMessageRepository();
        $newContactMessagesCount = $contactRepo->countByStatus('new');

        $this->renderView('dashboard-administrateur', [
            'pageTitle' => 'DASHBOARD ADMINISTRATEUR',
            'usersCount' => $usersCount,
            'pendingTechnicalReportsCount' => $pendingTechnicalReportsCount,
            'newContactMessagesCount' => $newContactMessagesCount,
        ]);
    }
}
