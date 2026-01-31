<?php
    class TripIncidentController extends BaseController {
        public function tripIncident(){
            $this->renderView('signaler-incident', [
                'pageTitle' => 'Signaler un Incident',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }