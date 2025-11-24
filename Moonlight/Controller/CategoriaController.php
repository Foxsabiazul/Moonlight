<?php
    namespace Moonlight\Controller;

    class CategoriaController extends Controller{

        // pagina de categorias.

        public function index($id, $link) {
            if(!empty($id)){ //se algum id foi enviado, será feita pesquisa na API, se houver sucesso, apresentará apenas jogos de uma categoria.

                //pegar jogos de uma categoria especifica.
                $url = "{$link}/api/jogospaginacao.php?page=1&categoria=". $id;
                $dadosJSON = file_get_contents($url);
                $dadosJogos = json_decode($dadosJSON) ?? [];

                //pegar o nome da categoria especifica
                $urlCat = "{$link}/api/categoria.php?id=". $id;
                $dadosCategoriaJSON = file_get_contents($urlCat);
                $dadosCategoria = json_decode($dadosCategoriaJSON) ?? [];

                if(!empty($dadosCategoria)){ //se tiver algum dado de categoria foi sucesso, vai resgatar jogos apenas daquela categoria.
                    $tituloCategoria = "Jogos de " . $dadosCategoria->nm_cat;
                } else{ // não conseguiu dado de categoria, então pega qualquer jogo e de qualquer categoria.
                    header("Location: " . BASE_URL . "/categoria");
                }
            } else{                    
                // PAGINAÇÃO
                // Carrega a primeira página (page=1) dos Jogos da API
                // http://localhost/Moonlight/Moonlight_Backend/public/api/jogospaginacao.php?page=1;
                $url = "{$link}/api/jogospaginacao.php?page=1"; 
                $dadosJSON = file_get_contents($url);
                $dadosJogos = json_decode($dadosJSON) ?? [];
                $tituloCategoria = "Jogos de todas as Categorias";

            }

            require "../Views/categoria/index.php";
        }
    }