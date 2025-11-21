<?php
    namespace Moonlight\Controller;

    class IndexController {

        public function index($id, $link) {
            require "../Views/index/index.php";
        }

        public function erro404(){
            require "../Views/index/erro.php";
        }
    }