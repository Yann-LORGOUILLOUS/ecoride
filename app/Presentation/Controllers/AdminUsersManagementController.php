<?php
    class AdminUsersManagementController extends BaseController {
        public function adminUsersManagement(){
            $this->renderView('gestion-comptes-utilisateurs', [
                'pageTitle' => 'Gestion des Comptes',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }