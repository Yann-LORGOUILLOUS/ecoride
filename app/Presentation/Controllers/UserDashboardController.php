<?php
    class UserDashboardController extends BaseController {
        public function userDashboard(){
            $this->renderView('mon-compte', [
                'pageTitle' => 'Mon Compte',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }