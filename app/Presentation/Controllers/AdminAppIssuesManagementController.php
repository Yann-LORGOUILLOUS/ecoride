<?php
    class AdminAppIssuesManagementController extends BaseController {
        public function adminAppIssuesManagement(){
            $this->renderView('gestion-signalements-techniques', [
                'pageTitle' => 'Gestion des Signalements Techniques',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }