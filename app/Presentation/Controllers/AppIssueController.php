<?php
    class AppIssueController extends BaseController {
        public function appIssue(){
            $this->renderView('signaler-probleme-technique', [
                'pageTitle' => 'Signaler un Problème Technique',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }