<?php
namespace Moonlight\Controller;

class GamesController extends Controller
{

    public function index($id, $link)
    {
        $url = "{$link}/api/jogo.php?id=" . $id;
        $dadosJogoJSON = file_get_contents($url);
        
        if ($dadosJogoJSON === FALSE) {
        } else {
            $dadosDecodificados = json_decode($dadosJogoJSON);
            $dadosJogo = null;
            $arrayDeJogos = (is_array($dadosDecodificados)) ? $dadosDecodificados : [$dadosDecodificados];

            foreach ($arrayDeJogos as $jogo) {
                if (is_object($jogo)) {
                    $dadosJogo = $jogo;
                    break;
                }
            }
        }
        if (!empty($dadosJogo) && isset($dadosJogo->titulo)) {
            $tituloJogo = $dadosJogo->titulo;
        } else {
            $tituloJogo = "Jogo Não Encontrado";
            $dadosJogo = null;
        }


        require "../Views/games/index.php";
    }
}