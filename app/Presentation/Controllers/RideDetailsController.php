<?php
    class RideDetailsController extends BaseController {
        public function rideDetails(){
            $this->renderView('details-trajet', [
                'pageTitle' => 'Détails du Trajet',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }