<?php

require_once __DIR__ . '/BaseController.php';

class LegalNoticesController extends BaseController
{
    public function legalNotices(): void
    {
        $this->renderView('mentions-legales', [
            'pageTitle' => 'Mentions légales',
        ]);
    }
}
