<?php
    namespace Moonlight\Controller;

    class CategoriaController {

        public function index($id, $link) {
            $url = "{$link}/api/jogos.php?categoria=". $id;
            $dadosJogos = file_get_contents($url);
            $dadosJogos = json_decode($dadosJogos);

            $urlCat = "{$link}/api/categoria.php?id=". $id;
            $dadosCategoria = file_get_contents($urlCat);
            $dadosCategoria = json_decode($dadosCategoria);

            require "../Views/categoria/index.php";
        }
    }