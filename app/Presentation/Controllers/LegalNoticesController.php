<?php
    class LegalNoticesController extends BaseController {
        public function legalNotices(){
            $this->renderView('mentions-legales', [
                'pageTitle' => 'Mentions Légales',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }