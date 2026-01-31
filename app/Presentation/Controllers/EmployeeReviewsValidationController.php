<?php
    class EmployeeReviewsValidationController extends BaseController {
        public function employeeReviewsValidation(){
            $this->renderView('valider-avis', [
                'pageTitle' => 'Validation des Avis',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }