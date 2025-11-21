<?php
    namespace Moonlight\Controller;

    class CategoriaController {

        // pagina de categorias.

        public function index($id, $link) {
            if(!empty($id)){ //se algum id foi enviado, será feita pesquisa na API, se houver sucesso, apresentará apenas jogos de uma categoria.

                //pegar jogos de uma categoria especifica.
                $url = "{$link}/api/jogos.php?categoria=". $id;
                $dadosJogos = file_get_contents($url);
                $dadosJogos = json_decode($dadosJogos);

                //pegar o nome da categoria especifica
                $urlCat = "{$link}/api/categoria.php?id=". $id;
                $dadosCategoria = file_get_contents($urlCat);
                $dadosCategoria = json_decode($dadosCategoria);

                if(!empty($dadosCategoria)){ //se tiver algum dado de categoria foi sucesso, vai resgatar jogos apenas daquela categoria.
                    $tituloCategoria = "Jogos de " . $dadosCategoria->nm_cat;
                } else{ // não conseguiu dado de categoria, então pega qualquer jogo e de qualquer categoria.
                    $url = "{$link}/api/jogos.php";
                    $dadosJogos = file_get_contents($url);
                    $dadosJogos = json_decode($dadosJogos);
                    $tituloCategoria = "Jogos de todas as Categorias";
                }
            } else{
                // pegar qualquer jogo e de qualquer categoria.
                $url = "{$link}/api/jogos.php";
                $dadosJogos = file_get_contents($url);
                $dadosJogos = json_decode($dadosJogos);
                $tituloCategoria = "Jogos de todas as Categorias";

            }

            require "../Views/categoria/index.php";
        }
    }