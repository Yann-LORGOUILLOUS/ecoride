<?php
    class UserCreditsController extends BaseController {
        public function userCredits(){
            $this->renderView('mes-credits', [
                'pageTitle' => 'Mes Crédits',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }