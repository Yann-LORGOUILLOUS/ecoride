<?php
    class AdminDashboardController extends BaseController {
        public function adminDashboard(){
            $this->renderView('dashboard-administrateur', [
                'pageTitle' => 'DASHBOARD ADMINISTRATEUR',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }