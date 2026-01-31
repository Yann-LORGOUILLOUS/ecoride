<?php
    class RidesController extends BaseController {
        public function rides(){
            $this->renderView('trajets', [
                'pageTitle' => 'Trajets',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }