<?php
    class UserReviewsController extends BaseController {
        public function userReviews(){
            $this->renderView('mes-avis', [
                'pageTitle' => 'Mes Avis',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }