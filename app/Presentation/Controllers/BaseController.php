<?php 
    class BaseController {
        protected function renderView ($view, $data = []){
            extract ($data);
            require __DIR__ . '/../Views/pages/' . $view . '.php';
        }
    }