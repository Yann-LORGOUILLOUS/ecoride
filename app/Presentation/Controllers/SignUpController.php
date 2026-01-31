<?php
    class SignUpController extends BaseController {
        public function signUp(){
            $this->renderView('inscription', [
                'pageTitle' => 'Inscription',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }