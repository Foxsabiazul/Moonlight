<?php
    namespace Moonlight_Backend\Controller;

    use Moonlight_Backend\config\Conexao;
    use Moonlight_Backend\Model\UsuarioModel;

    class IndexController {
        private $usuario;

        public function __construct() {
            $db = new Conexao();
            $pdo = $db->connect();
            $this->usuario = new UsuarioModel($pdo);
        }

        public function index() {
            require "../Views/index/index.php";
        }

        // Método para verificar as credenciais do usuário
        public function verificar($email, $senha) {
            $dadosUsuario = $this->usuario->getUsuario($email);
            $loginFalhou = false;

            if(empty($dadosUsuario->id_user)) {
                $_SESSION['modalTitle'] = "Usuario Inválido";
                $_SESSION['modalMessage'] = "Usuario não foi encontrado.";
                $loginFalhou = true;

            } else if(!password_verify($senha, $dadosUsuario->senha)){
                /**
                 * ATENÇÃO: APENAS PARA NÃO PERDER TEMPO BATENDO CABEÇA IGUAL EU,
                 * SE A SENHA DO BANCO NÃO ESTÁ COM HASH
                 * ele vai falhar na comparação mesmo que você tenha acertado a senha
                 * que está inserida no banco.
                 * password_verify serve para verificar se a senha bate com algum hash que ele pode ter feito
                 * se não bate com o hash aí ele manda um false e cai aqui dentro.
                 */
                $_SESSION['modalTitle'] = "Email ou Senha Invalidos";
                $_SESSION['modalMessage'] = "Os dados fornecidos não coincidem.";
                $loginFalhou = true;
            }

            if($loginFalhou){
                echo "<script>location.href='index';</script>";
                exit;
            } else{
                $_SESSION['Logado_Na_Sessão'] = array(
                    "id_user"=>$dadosUsuario->id_user,
                    "nm_user"=>$dadosUsuario->nm_user,
                    "data_criacao"=>$dadosUsuario->data_criacao);

                echo "<script>location.href='index';</script>";
                exit;
            }
        }

        public function logout(){
            unset($_SESSION['Logado_Na_Sessão']);
            echo "<script>location.href='index';</script>";
        }

        // Outros métodos do controlador podem ser adicionados aqui
    }