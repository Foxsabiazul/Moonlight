<?php

    namespace Moonlight_Backend\Controller;

    use Moonlight_Backend\config\Sanitizador;
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
        if(empty($id)){
            $_SESSION['modalTitle'] = "Registro Inválido";
            $_SESSION['modalMessage'] = "O registro fornecido é inválido.";
        } else{
            $mensagem = $this->usuario->excluir($id);
            if($mensagem == 1){
                $_SESSION['modalTitle'] = "Registro excluído";
                $_SESSION['modalMessage'] = "O registro fornecido foi excluido.";
            } else{
                $_SESSION['modalTitle'] = "O Registro não foi excluído";
                $_SESSION['modalMessage'] = "O registro fornecido não foi excluido por alguma falha interna.";
            }
        }

        header("Location: " . BASE_URL . "/usuario/listar");
        
    }

    public function salvar() {
        // require "../Views/usuario/salvar.php";
        $id = $_POST["id"] ?? NULL;
        $nome = $_POST["nome"] ?? NULL;
        $email = $_POST["email"] ?? NULL;
        $senha = $_POST["senha"] ?? NULL;
        $senha2 = $_POST["senha2"] ?? NULL;

        $loginFalhou = false;

        if (empty($nome)) {
            $_SESSION['modalTitle'] = "Nome de usuario Inválido";
            $_SESSION['modalMessage'] = "Por favor, preencha o nome do usuário.";
            $loginFalhou = true;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['modalTitle'] = "Email inválido";
            $_SESSION['modalMessage'] = "Por favor, preencha com um e-mail válido.";
            $loginFalhou = true;
        } else if ((empty($id)) && (empty($senha))) {
            $_SESSION['modalTitle'] = "Senha inválida";
            $_SESSION['modalMessage'] = "Por favor, preencha a senha.";
            $loginFalhou = true;
        } else if ($senha != $senha2) {
            $_SESSION['modalTitle'] = "Senha inválida";
            $_SESSION['modalMessage'] = "As senhas não estão iguais.";
            $loginFalhou = true;
        } 

        if($loginFalhou){
            header("Location: " . BASE_URL . "/usuario");
            exit;
        } else{
            $mensagem = $this->usuario->salvar();
            if($mensagem == 1){
                $_SESSION['modalTitle'] = "Operação realizada com sucesso.";
                $_SESSION['modalMessage'] = "Usuario salvo";
            } else{
                $_SESSION['modalTitle'] = "Falha na Operação.";
                $_SESSION['modalMessage'] = "O usuario não foi salvo por alguma falha interna.";
            }
            header("Location: " . BASE_URL . "/usuario/listar");
            exit;
        }
    }

    public function listar() {
        require "../Views/usuario/listar.php";
    }

    public function logout(){
        unset($_SESSION['Logado_Na_Sessão']);
        header("Location: " . BASE_URL . "/index");
        exit;
    }

    public function sanitizacaoValidacaoInicial(): array
    {
        $email = Sanitizador::sanitizar($_POST['email']);
        $senha = Sanitizador::sanitizar($_POST['senha']);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            /**
             * modalTitle e Message servem para dar mensagens personalizadas ao usuario com um Modal.
             * nao se preocupe com a ativação do modal, ele é ativado sozinho quando chega uma mensagem à ele.
             * ele está em Views/Components/FlashMessage.php
             */
            $_SESSION['modalTitle'] = "E-mail Inválido.";
            $_SESSION['modalMessage'] = "O Email Inserido é inválido. Tente Novamente com um válido.";
            header("Location: " . BASE_URL . "/index");
            exit;
        } else if (empty($senha)) {
            $_SESSION['modalTitle'] = "Digite a senha.";
            $_SESSION['modalMessage'] = "A senha não está preenchida no formulário. Digite a senha.";
            header("Location: " . BASE_URL . "/index");
            exit;
        }

        $cleanData["email"] = $email;
        $cleanData["senha"] = $senha;

        return $cleanData;
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
            header("Location: " . BASE_URL . "/index");
            exit;
        } else{
            $_SESSION['Logado_Na_Sessão'] = array(
                "id_user"=>$dadosUsuario->id_user,
                "nm_user"=>$dadosUsuario->nm_user,
                "data_criacao"=>$dadosUsuario->data_criacao);

            header("Location: " . BASE_URL . "/index");
            exit;
        }
    }
	
}