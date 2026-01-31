<?php
    class EmployeeTripsIncidentsManagementController extends BaseController {
        public function employeeTripsIncidentsManagement(){
            $this->renderView('gestion-signalements-trajets', [
                'pageTitle' => 'Gestion des Signalements',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }