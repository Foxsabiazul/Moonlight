<?php
namespace Moonlight\Controller;

use Moonlight\config\Conexao;
use Moonlight\Config\Logger;
use Moonlight\Model\BibliotecaModel;

class GamesController extends Controller
{

    private BibliotecaModel $biblioteca;

    public function __construct() {
        $pdo = Conexao::connect();
        $this->biblioteca = new BibliotecaModel($pdo);
    }

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

        //logica de verificação (se usuario ja comprou o jogo e está em sua biblioteca.)
        $possuiJogo = false;

        if(!empty($dadosJogo) && isset($_SESSION["Logado_Na_Sessão"]["id_user"])){
            $id_user = (int)$_SESSION["Logado_Na_Sessão"]["id_user"];
            $id_games = (int)$dadosJogo->id_games;

            try {
                
                $possuiJogo = $this->biblioteca->usuarioPossuiJogo($id_user, $id_games);
                
            } catch (\Exception $e) {
                // Tratar exceção do banco de dados (por exemplo, logar e manter $possuiJogo = false)
                $possuiJogo = false;
                Logger::logError($e, "GAMES_CONTROLLER_ERROR_INDEX");
                $_SESSION["modalTitle"] = "Erro inesperado";
                $_SESSION["modalMessage"] = "Falha inesperada.";
            }
            
        }

        require "../Views/games/index.php";
    }
}