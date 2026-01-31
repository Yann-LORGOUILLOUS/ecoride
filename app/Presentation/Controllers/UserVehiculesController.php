<?php
    class UserVehiculesController extends BaseController {
        public function userVehicules(){
            $this->renderView('mes-vehicules', [
                'pageTitle' => 'Mes Véhicules',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }