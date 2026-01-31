<?php
    class UserRidesController extends BaseController {
        public function userRides(){
            $this->renderView('mes-trajets', [
                'pageTitle' => 'Mes Trajets',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }