<?php
    class ContactUsController extends BaseController {
        public function contactUs(){
            $this->renderView('contact', [
                'pageTitle' => 'Nous Contacter',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }