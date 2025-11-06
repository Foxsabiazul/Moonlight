<?php

    namespace Moonlight_Backend\Controller;

    use Moonlight_Backend\config\Conexao;
    use Moonlight_Backend\Model\UsuarioModel;

class UsuarioController{

    private $usuario;

    public function __construct(){
        $db = new Conexao();
        $pdo = $db->connect();
        $this->usuario = new UsuarioModel($pdo);
    }

    public function index($id) {
        require "../Views/usuario/index.php";
    }

    public function excluir($id) {
        require "../Views/usuario/excluir.php";
    }

    public function salvar() {
        require "../Views/usuario/salvar.php";
    }

    public function listar() {
        require "../Views/usuario/listar.php";
    }
	
}