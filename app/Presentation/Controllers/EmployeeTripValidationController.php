<?php
    class EmployeeTripValidationController extends BaseController {
        public function employeeTripValidation(){
            $this->renderView('valider-trajet', [
                'pageTitle' => 'Validation du Trajet',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }