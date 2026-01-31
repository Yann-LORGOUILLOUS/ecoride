<?php
    class UserReservationsController extends BaseController {
        public function userReservations(){
            $this->renderView('mes-reservations', [
                'pageTitle' => 'Mes Réservations',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }