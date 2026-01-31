<?php
    class AdminStatsController extends BaseController {
        public function adminStats(){
            $this->renderView('statistiques', [
                'pageTitle' => 'Statistiques',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }