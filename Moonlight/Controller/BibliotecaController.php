<?php
    namespace Moonlight\Controller;

    class BibliotecaController extends Controller {

        public function index($id, $link) {
            require "../Views/biblioteca/index.php";
        }
    }