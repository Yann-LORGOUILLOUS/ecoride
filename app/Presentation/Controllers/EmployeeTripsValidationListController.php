<?php
    class EmployeeTripsValidationListController extends BaseController {
        public function employeeTripsValidationList(){
            $this->renderView('liste-trajets-a-valider', [
                'pageTitle' => 'Trajets à Valider',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }