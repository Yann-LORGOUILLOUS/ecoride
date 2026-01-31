<?php
    class UserInfosController extends BaseController {
        public function userInfos(){
            $this->renderView('mes-informations', [
                'pageTitle' => 'Mes Informations',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }