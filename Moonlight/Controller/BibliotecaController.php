<?php
    namespace Moonlight\Controller;

use Moonlight\config\Conexao;
use Moonlight\Model\BibliotecaModel;

    class BibliotecaController extends Controller {

        private $biblioteca;

        public function __construct(){
            // para fazer conexão singleton
            $pdo = Conexao::connect();
            $this->biblioteca = new BibliotecaModel($pdo);
        }

        public function index($id = null, $link) {

            parent::isProtected();

            $id_user = (int)$_SESSION["Logado_Na_Sessão"]["id_user"];

            $dadosJogosBiblioteca = $this->biblioteca->listarJogosBiblioteca($id_user) ?? [];

            require "../Views/biblioteca/index.php";
        }
    }