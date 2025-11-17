<?php
    namespace Moonlight_Backend\Controller;

    use Moonlight_Backend\config\Conexao;
    use Moonlight_Backend\Model\UsuarioModel;

    class IndexController {
        private $usuario;

        public function __construct() {
            $pdo = Conexao::connect();
            $this->usuario = new UsuarioModel($pdo);
        }

        public function index() {
            require "../Views/index/index.php";
        }

        // Outros métodos do controlador podem ser adicionados aqui
    }