<?php

    namespace Moonlight\Controller;

    use Moonlight\config\Sanitizador;
    use Moonlight\config\Conexao;
    use Moonlight\Config\Logger;
    use Moonlight\Config\ModalMessage;
    use Moonlight\Model\UsuarioModel;
    use PDO;
use Throwable;

class UsuarioController extends Controller{
    private PDO $pdo;
    private $usuario;

    public function __construct(){
        // para fazer conexão singleton
        $pdo = Conexao::connect();
        $this->usuario = new UsuarioModel($pdo);
    }

    public function index() {
        parent::isProtected();
        
        $userName = isset($_SESSION['Logado_Na_Sessão']) ? htmlspecialchars($_SESSION['Logado_Na_Sessão']["nm_user"]) : "Usuário";
        $email = isset($_SESSION['Logado_Na_Sessão']) ? htmlspecialchars($_SESSION['Logado_Na_Sessão']["email"]) : "user@email.com";
        
                
        require "../Views/usuario/index.php";
    }

    public function access() {
        require "../Views/usuario/login.php";
    }

    public function signup(){
        require "../Views/usuario/cadastro.php";
    }

    // metódo de sanitização para limpar os espaços vazios que vem de formulario.
    public function sanitizacao(array $inputData): array
    {
        $nome = Sanitizador::sanitizar($inputData["nome"] ?? '');
        $email = Sanitizador::sanitizar($inputData['email'] ?? '');
        $senha = Sanitizador::sanitizar($inputData['senha'] ?? '');
        $senhaRedigitada = Sanitizador::sanitizar($inputData["senha2"] ?? '');

        return[
            "nm_user" => $nome,
            "email" => $email,
            "senha" => $senha,
            "senhaRedigitada" => $senhaRedigitada
        ];

    }

    // salvar é ativado sozinho como rota de formulario enviado nessa url aqui: /usuario/salvar
    public function salvar() {
        
        // pegamos tudo o que vem do formulario
        $inputData = $_POST;

        /**
         *  VALIDAÇÃO DE FORMULÁRIO (PRIMEIRA CAMADA)
         */

        $cleanData = $this->sanitizacao($inputData); // Pega e sanitiza o que veio do formulario de Usuario.
        $validacaoFalhou = false;

        if (empty($cleanData['nm_user'])) { // nome de usuario necessario
            /**
             * modalTitle e Message servem para dar mensagens personalizadas ao usuario com um Modal.
             * nao se preocupe com a ativação do modal, ele é ativado sozinho quando chega uma mensagem à ele.
             * ele está em Views/Components/FlashMessage.php
             */
            $_SESSION['modalTitle'] = "Nome de usuario Inválido";
            $_SESSION['modalMessage'] = "Por favor, preencha o nome do usuário.";
            $validacaoFalhou = true;
        } else if(empty($cleanData['email'])){
            $_SESSION['modalTitle'] = "E-mail Inválido.";
            $_SESSION['modalMessage'] = "O Email Inserido é inválido. Tente Novamente com um válido.";
            $validacaoFalhou = true;
        } else if (!filter_var($cleanData['email'], FILTER_VALIDATE_EMAIL)) { // email precisa ser valido
            $_SESSION['modalTitle'] = "Email inválido";
            $_SESSION['modalMessage'] = "Por favor, preencha com um e-mail válido.";
            $validacaoFalhou = true;
        } else if (empty($cleanData['senha'])) { // se for um registro novo de usuario, a senha precisa estar preenchida (se for atualização aí tanto faz, vai continuar a mesma senha do banco).
            $_SESSION['modalTitle'] = "Senha inválida";
            $_SESSION['modalMessage'] = "Por favor, preencha a senha.";
            $validacaoFalhou = true;
        } else if ($cleanData['senha'] != $cleanData['senhaRedigitada']) { // se a senha for diferente da que está redigitada.
            $_SESSION['modalTitle'] = "Senha inválida";
            $_SESSION['modalMessage'] = "As senhas não estão iguais.";
            $validacaoFalhou = true;
        }

        if($validacaoFalhou){ // se validacao falhou é true ou existe, então só redirecione o usuario para /usuario
            header("Location: " . BASE_URL . "/usuario/signup");
            exit;
        }

        // operação vai tentar ir pro banco
        $this->persistirUsuario($cleanData);
    }

    private function persistirUsuario(array $cleanData){

        // OPERAÇÃO DE BANCO DE DADOS (SEGUNDA CAMADA com Exceções)
        try{
            // Prepara os dados
            $cleanData['tipo'] = "cliente";
            $cleanData['senha'] = password_hash($cleanData['senha'], PASSWORD_DEFAULT);
            $cleanData['data_criacao'] = (new \DateTime())->format('Y-m-d H:i:s');

            // Insere o usuário no banco (aqui pode lançar ModalMessage ou PDOException)
            $id = $this->usuario->inserirUsuario($cleanData); // Chamada ÚNICA!

            // SUCESSO! (O ID foi retornado sem exceções)
            $cleanData['id_user'] = $id; 
            
            $_SESSION['Logado_Na_Sessão'] = [
                "id_user" => $cleanData['id_user'], 
                "nm_user" => $cleanData['nm_user'],
                "email" => $cleanData['email'],
                "data_criacao" => $cleanData['data_criacao'],
                "tipo"=> $cleanData['tipo']
            ];
            $_SESSION['modalTitle'] = "Cadastro realizado com sucesso.";
            $_SESSION['modalMessage'] = "Usuário salvo e login efetuado.";
        } catch (ModalMessage $e) {

            // **ERRO DE REGRA DE NEGÓCIO** (Ex: E-mail duplicado)
            // Logamos para ter histórico, mas o erro é amigável.
            Logger::logError($e, "BUSINESS_RULE");

            $_SESSION['modalTitle'] = $e->getTitle();
            $_SESSION['modalMessage'] = $e->getMessage();
            
        } catch (\PDOException $e) {

            // **ERRO GRAVE DE BANCO DE DADOS** (Ex: Conexão, SQL malformado, Coluna faltando)
            // Logamos o erro de forma mais detalhada.
            Logger::logError($e, "DATABASE_ERROR");

            $_SESSION['modalTitle'] = "Falha no Sistema";
            $_SESSION['modalMessage'] = "Ocorreu uma falha interna ao salvar o usuário. Tente novamente mais tarde.";
            
        } catch (\Throwable $e) {
            // **ERRO GENÉRICO** (Ex: Classes não encontradas, erros de código)
            // Logamos tudo que for inesperado.
            Logger::logError($e, "CRITICAL_ERROR");

            $_SESSION['modalTitle'] = "Erro Crítico";
            $_SESSION['modalMessage'] = "O sistema encontrou um erro crítico inesperado.";
        }

        header("Location: " . BASE_URL . "/usuario/signup");
        exit;
    }

    // usado no index.php da public para fazer o login do usuario:
    public function login() {

        // pegamos tudo o que vem do formulario
        $inputData = $_POST;
        $cleanData = $this->sanitizacao($inputData); // Pega e sanitiza o que veio do formulario de login

        $email = $cleanData["email"]; // transformamos os dados do array em variaveis para facilitar leitura.
        $senha = $cleanData["senha"];


        // VALIDAÇÃO DE FORMULÁRIO (PRIMEIRA CAMADA)

        if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $_SESSION['modalTitle'] = "E-mail Inválido.";
            $_SESSION['modalMessage'] = "O Email Inserido é inválido. Tente Novamente com um válido.";
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        } else if (empty($senha)) {
            $_SESSION['modalTitle'] = "Digite a senha.";
            $_SESSION['modalMessage'] = "A senha não está preenchida no formulário.";
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        }

        /**
         * verificamos se os dados batem com o do banco e permitimos o login se estiver correto!
         */

        try{
            $dadosUsuario = $this->verificarUsuarioPorEmail($email, $senha);
            
            $_SESSION['Logado_Na_Sessão'] = array(
                "id_user"=>$dadosUsuario->id_user,
                "nm_user"=>$dadosUsuario->nm_user,
                "email" => $email,
                "data_criacao"=>$dadosUsuario->data_criacao,
                "tipo"=>$dadosUsuario->tipo
            );

            header("Location: " . BASE_URL . "/index");
            exit;
        
        } catch (\Moonlight\Config\ModalMessage $e) {
            
            // **ERRO DE REGRA DE NEGÓCIO** (Usuário não encontrado ou senha errada)
            \Moonlight\Config\Logger::logError($e, "LOGIN_FAILED");

            $_SESSION['modalTitle'] = $e->getTitle();
            $_SESSION['modalMessage'] = $e->getMessage();
            
        } catch (\PDOException $e) {
            // **ERRO GRAVE DE BANCO DE DADOS**
            \Moonlight\Config\Logger::logError($e, "DATABASE_ERROR");

            $_SESSION['modalTitle'] = "Falha Crítica";
            $_SESSION['modalMessage'] = "Ocorreu uma falha interna no sistema de login.";
            
        } catch (\Throwable $e) {
            // **ERRO GENÉRICO**
            \Moonlight\Config\Logger::logError($e, "CRITICAL_ERROR");

            $_SESSION['modalTitle'] = "Erro Crítico";
            $_SESSION['modalMessage'] = "O sistema encontrou um erro inesperado.";
        }

        header("Location: " . BASE_URL . "/usuario/access");
        exit;
    }

    // Método usado pro metodo login verificar as credenciais do usuário no banco por meio do email e comparando as senhas.
    private function verificarUsuarioPorEmail(string $email, string $senha): object {

        $dadosUsuario = $this->usuario->buscarPorEmail($email);

        if (empty($dadosUsuario->id_user)) {
            // Lança exceção de REGRA DE NEGÓCIO (Usuário não encontrado)
            throw new ModalMessage(
                "Credenciais Inválidas", 
                "Usuário com o e-mail '{$email}' não foi encontrado."
            );
        } else if(!password_verify($senha, $dadosUsuario->senha)){
            //se senha no banco não estiver com hash ou a senha nao bate com o hash, cai aqui.

            // Lança exceção de REGRA DE NEGÓCIO (Senha incorreta)
            throw new ModalMessage(
                "Credenciais Inválidas",
                "A senha fornecida para o e-mail '{$email}' está incorreta."
            );
        }

        return $dadosUsuario;
    }

    public function atualizarInformacoesPessoais(){
        parent::isProtected();

        $id_user = $_SESSION["Logado_Na_Sessão"]["id_user"];

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . BASE_URL . "/index");
            exit;
        }

        $inputData = $_POST;
        $cleanData = $this->sanitizacao($inputData);

        $cleanData['id_user'] = $id_user;
        
        if (empty($cleanData['nm_user']) || empty($cleanData['email']) || !filter_var($cleanData['email'], FILTER_VALIDATE_EMAIL) || empty($cleanData['senha'])) {
            $_SESSION['modalTitle'] = "Erro de Validação";
            $_SESSION['modalMessage'] = "Por favor, preencha o nome, um e-mail válido e a senha.";
            header("Location: " . BASE_URL . "/usuario");
            exit;
        } else if($cleanData['senha'] != $cleanData['senhaRedigitada']) { // se a senha for diferente da que está redigitada.
            $_SESSION['modalTitle'] = "Senha inválida";
            $_SESSION['modalMessage'] = "As senhas não estão iguais.";
            header("Location: " . BASE_URL . "/usuario");
            exit;
        }

        try{
            
            $cleanData['senha'] = password_hash($cleanData['senha'], PASSWORD_DEFAULT);

            $sucesso = $this->usuario->atualizarUsuario($cleanData);

            if($sucesso){
                $_SESSION["Logado_Na_Sessão"]["nm_user"] = $cleanData['nm_user'];
                $_SESSION['Logado_Na_Sessão']['email'] = $cleanData['email'];

                $_SESSION['modalTitle'] = "Sucesso!";
                $_SESSION['modalMessage'] = "Suas informações pessoais foram salvas com sucesso.";
            } else {
                $_SESSION['modalTitle'] = "Deu ruim!";
                $_SESSION['modalMessage'] = "Nenhuma alteração foi detectada ou salva no banco de dados.";
                 throw new \Exception("Nenhuma alteração foi detectada ou salva no banco de dados.");
            }
        } catch(ModalMessage $e){
            Logger::logError($e, "BUSINESS_RULE");

            $_SESSION['modalTitle'] = $e->getTitle();
            $_SESSION['modalMessage'] = $e->getMessage();
        } catch(\Throwable $e){
            Logger::logError($e, "UPDATE_CRITICAL_ERROR");
            $_SESSION['modalTitle'] = "Erro Crítico";
            $_SESSION['modalMessage'] = "Ocorreu uma falha inesperada ao tentar salvar suas informações.";
        }

        header("Location: " . BASE_URL . "/usuario");
        exit;
    }

    public function logout(){
        /**
         * tiramos o $_SESSION da sessão logada do usuario.
         */
        unset($_SESSION['Logado_Na_Sessão']);
        /**
         * mandamos ele de volta pra pagina inicial.
         */
        header("Location: " . BASE_URL . "/index");
        exit;
    }
	
}