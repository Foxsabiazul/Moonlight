<?php
    namespace Moonlight\Controller;

use DateTime;

    class SearchController {

        // pagina de categorias.

        public function index() {

            $termoBusca = $_GET['termo'] ?? NULL;
            $order = $_GET['order'] ?? NULL;
            $filtro = $_GET['filtro'] ?? NULL;
            $operador = $_GET['operador'] ?? NULL;
            $categoria = $_GET['categoria'] ?? NULL;
            $page = $_GET['page'] ?? 1; // Para suportar a paginação inicial

            $link = "http://localhost/Moonlight/Moonlight_Backend/public";
            $queryString = "page={$page}";
            
            if (!empty($termoBusca)) {
                $queryString .= "&termo=" . urlencode($termoBusca);

                if($filtro == "data_lancamento"){
                    $dateObj = \DateTime::createFromFormat('Y-m-d', $termoBusca);
                    
                    if($dateObj){
                        $termoFormatado = $dateObj->format('d/m/Y');
                    } else{
                        $termoFormatado = htmlspecialchars($termoBusca);
                    }

                    $operadorTexto = [
                        '=' => 'lançados em',
                        '>' => 'lançados após',
                        '<' => 'lançados antes de'
                    ][$operador] ?? 'por data';

                    $tituloBusca = "Jogos {$operadorTexto}: {$termoFormatado}";
                } else if($filtro == "preco"){

                    $termoFormatado = number_format($termoBusca, 2, ',', '.');

                    $operadorTexto = [
                        '=' => 'com preço igual a',
                        '>' => 'com preço maior do que',
                        '<' => 'com preço menor do que'
                    ][$operador] ?? 'por preço';
                    $tituloBusca = "Jogos {$operadorTexto}: R$ {$termoFormatado}";
                } else{
                    $tituloBusca = "Resultados para: " . htmlspecialchars($termoBusca);
                }
            } else {
                // Se não houver termo, mostra todos, e a API deve ignorar os filtros vazios
                $tituloBusca = "Nenhum termo de busca fornecido. Exibindo jogos de todas as categorias.";
            }

            if (!empty($order)) {
                $queryString .= "&order=" . urlencode($order);
            }
            if (!empty($filtro)) {
                $queryString .= "&filtro=" . urlencode($filtro);
            }
            if (!empty($operador)) {
                $queryString .= "&operador=" . urlencode($operador);
            }
            if(!empty($categoria)){
                $queryString .= "&categoria=" . urlencode($categoria);
            }
            
            // Monta a URL final para a API
            $url = "{$link}/api/jogospaginacao.php?{$queryString}";
            // Busca os dados
            $dadosJSON = file_get_contents($url);

            $dadosJogos = json_decode($dadosJSON) ?? [];
        

            require "../Views/search/index.php";
        }
    }