<?php
    class LoginController extends BaseController {
        public function login(){
            $this->renderView('connexion', [
                'pageTitle' => 'Connexion',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }