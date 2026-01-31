<?php
    class UsersPublicInfosController extends BaseController {
        public function usersPublicInfos(){
            $this->renderView('profils', [
                'pageTitle' => 'Profils',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }