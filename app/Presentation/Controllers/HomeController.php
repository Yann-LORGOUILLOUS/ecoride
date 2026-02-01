<?php
    class HomeController extends BaseController {
        public function index(){
            $this->renderView('accueil', [
                'pageTitle' => 'Accueil',
            ]);
        }
    }