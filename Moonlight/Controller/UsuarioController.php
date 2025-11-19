<?php

    namespace Moonlight\Controller;

    use Moonlight\config\Sanitizador;
    use Moonlight\config\Conexao;
    use Moonlight\Model\UsuarioModel;
    use PDO;

class UsuarioController{
    private PDO $pdo;
    private $usuario;

    public function __construct(){
        // para fazer conexão singleton
        $pdo = Conexao::connect();
        $this->usuario = new UsuarioModel($pdo);
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

    // a index é pagina de cadastro ou seja é um (insert ou update) então por isso tem o $id de parametração.
    // o id serve pra receber o que vêm da URL.
    /**
     * O Papel do Parâmetro $id
     * O parâmetro $id no UsuarioController index($id) vem da URL (geralmente como o terceiro segmento, ex: /usuario/index/5).
     * Se $id está vazio/nulo: A URL é do tipo /usuario ou /usuario/index. O fluxo é de Inserção (Novo Cadastro). O bloco no topo inicializa as variáveis $nome, $email, etc., como NULL ou vazias, e o formulário é carregado em branco.
     * Se $id tem um valor (ex: 5): A URL é do tipo /usuario/index/5. O fluxo é de Atualização (Edição).
     */
    public function index() {
        require "../Views/usuario/index.php";
    }

    public function access() {
        require "../Views/usuario/login.php";
    }

    public function signup(){
        require "../Views/usuario/cadastro.php";
    }

    // salvar é ativado sozinho como rota de formulario enviado nessa url aqui: /usuario/salvar
    public function salvar() {
        
        // pegamos tudo o que vem do formulario
        $inputData = $_POST;
        $cleanData = $this->sanitizacao($inputData); // Pega e sanitiza o que veio do formulario de Usuario.

        /**
         * fazemos validação aqui
         */

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
        } else{

            // OPERAÇÃO: INSERT
            // Senha é obrigatória no insert (validado acima)
            $cleanData['tipo'] = "cliente";
            $cleanData['senha'] = password_hash($cleanData['senha'], PASSWORD_DEFAULT);
            $cleanData['data_criacao'] = (new \DateTime())->format('Y-m-d H:i:s');
            $mensagem = $this->usuario->inserirUsuario($cleanData);

            // CAPTURA O ID (ou false se falhar)
            $id = $this->usuario->inserirUsuario($cleanData); 

            if($id > 0){ // operação de sucesso
                
                $cleanData['id_user'] = $id; 
                
                $_SESSION['Logado_Na_Sessão'] = array(
                    "id_user" => $cleanData['id_user'], 
                    "nm_user" => $cleanData['nm_user'],
                    "data_criacao" => $cleanData['data_criacao'],
                    "tipo"=> $cleanData['tipo']
                );
                $_SESSION['modalTitle'] = "Cadastro realizado com sucesso.";
                $_SESSION['modalMessage'] = "Usuário salvo";
            } else if ($id == -1) { // e-mail duplicado (retorno -1 do Model)
                
                $_SESSION['modalTitle'] = "E-mail já cadastrado.";
                $_SESSION['modalMessage'] = "O e-mail " . htmlspecialchars($cleanData['email']) . " já está sendo usado. Tente fazer login.";
            
            } else{ // operação de erro
                $_SESSION['modalTitle'] = "Falha na Operação.";
                $_SESSION['modalMessage'] = "O usuario não foi salvo por alguma falha interna.";
            }

            header("Location: " . BASE_URL . "/usuario/signup"); // redirecione o usuario para a pagina de listagem.
            exit;
        }
    }

    // usado no index.php da public para fazer o login do usuario:
    public function login() {

        // pegamos tudo o que vem do formulario
        $inputData = $_POST;
        $cleanData = $this->sanitizacao($inputData); // Pega e sanitiza o que veio do formulario de login

        $email = $cleanData["email"]; // transformamos os dados do array em variaveis para facilitar leitura.
        $senha = $cleanData["senha"];

        /**
         * fazemos validação aqui
         */
        if(empty($email)){
            $_SESSION['modalTitle'] = "E-mail Inválido.";
            $_SESSION['modalMessage'] = "O Email Inserido é inválido. Tente Novamente com um válido.";
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['modalTitle'] = "E-mail Inválido.";
            $_SESSION['modalMessage'] = "O Email Inserido é inválido. Tente Novamente com um válido.";
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        } else if (empty($senha)) {
            $_SESSION['modalTitle'] = "Digite a senha.";
            $_SESSION['modalMessage'] = "A senha não está preenchida no formulário. Digite a senha.";
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        }

        /**
         * verificamos se os dados batem com o do banco e permitimos o login se estiver correto!
         */

        $this->verificar($email, $senha);
    }

    // Método usado pro metodo login verificar as credenciais do usuário no banco por meio do email e comparando as senhas.
    public function verificar(string $email, string $senha) {

        $dadosUsuario = $this->usuario->buscarPorEmail($email);
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
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        } else{
            $_SESSION['Logado_Na_Sessão'] = array(
                "id_user"=>$dadosUsuario->id_user,
                "nm_user"=>$dadosUsuario->nm_user,
                "data_criacao"=>$dadosUsuario->data_criacao,
                "tipo"=>$dadosUsuario->tipo
            );

            header("Location: " . BASE_URL . "/index");
            exit;
        }
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