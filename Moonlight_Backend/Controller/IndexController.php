<?php
    namespace Moonlight_Backend\Controller;

    use Moonlight_Backend\config\Conexao;
    use Moonlight_Backend\Model\UsuarioModel;
    use PDO;

    class IndexController {
        private $usuario;
    

        public function __construct() {
            $pdo = Conexao::connect();
            $this->usuario = new UsuarioModel($pdo);
        }

        public function index() {
            require "../Views/index/index.php";
        }

        public function verificar($dados){
        $email = trim($dados["email"] ?? NULL);
        $senha= trim($dados["senha"] ?? NULL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo "<script>mensagem('E-mail inválido','error','')</script>";
        } else if (empty($senha)){
            echo "<script>mensagem('Senha inválida','error','')</script>";
        }

        $dadosUsuario = $this->usuario->getDadosEmail($email);

        //print_r($dadosUsuario);
        if (empty($dadosUsuario->id)) {
            echo"<script>mensagem('Usuário inválido','error','')<script/>";
        }else if(!password_verify($senha, $dadosUsuario->senha)){
            echo"<script>mensagem('Senha inválida','error','')</script>";
        }else{
            $_SESSION["user"] = array(
            "id" => $dadosUsuario->id,
            "nome" => $dadosUsuario->nome
            );

        echo "<script>location.href='index.php'</script>";
            }
        }
        // Outros métodos do controlador podem ser adicionados aqui
    }