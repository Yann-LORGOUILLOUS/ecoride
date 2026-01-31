<?php
    class EmployeeDashboardController extends BaseController {
        public function employeeDashboard(){
            $this->renderView('dashboard-moderateur', [
                'pageTitle' => 'DASHBOARD MODERATEUR',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }