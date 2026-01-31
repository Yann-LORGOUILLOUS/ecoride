<?php
    class CreateRideController extends BaseController {
        public function createRide(){
            $this->renderView('creer-trajet', [
                'pageTitle' => 'Proposer un Trajet',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }