<?php
    class CreateReviewsController extends BaseController {
        public function createReviews(){
            $this->renderView('rediger-avis', [
                'pageTitle' => 'Rédiger un Avis',
                'flashMessage' => 'EN COURS DE CONSTRUCTION',
                'flashType' => 'info',
            ]);
        }
    }