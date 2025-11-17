<?php

    namespace Moonlight_Backend\Controller;

    use Moonlight_Backend\config\Sanitizador;
    use Moonlight_Backend\config\Conexao;
    use Moonlight_Backend\Model\CategoriaModel;

class CategoriaController{

    private $categoria;

    public function __construct(){
        // para fazer conexão singleton
        $pdo = Conexao::connect();
        $this->categoria = new CategoriaModel($pdo);
    }

    // metódo de sanitização para limpar os espaços vazios que vem de formulario.
    public function sanitizacao(array $inputData): array
    {
        $id = Sanitizador::sanitizar($inputData["id"] ?? '');
        $nm_cat = Sanitizador::sanitizar($inputData["nome"] ?? '');
        $desc_cat = Sanitizador::sanitizar($inputData["descricao"] ?? '');

        return[
            "id_categoria" => $id,
            "nm_cat" => $nm_cat,
            "desc_cat" => $desc_cat
        ];

    }

    // a index é pagina de cadastro ou seja é um (insert ou update) então por isso tem o $id de parametração.
    // o id serve pra receber o que vêm da URL.
    /**
     * O Papel do Parâmetro $id
     * O parâmetro $id no CategoriaController index($id) vem da URL (geralmente como o terceiro segmento, ex: /categoria/index/5).
     * Se $id está vazio/nulo: A URL é do tipo /categoria ou /categoria/index. O fluxo é de Inserção (Novo Cadastro). O bloco no topo inicializa as variáveis $nome, $email, etc., como NULL ou vazias, e o formulário é carregado em branco.
     * Se $id tem um valor (ex: 5): A URL é do tipo /categoria/index/5. O fluxo é de Atualização (Edição).
     */
    public function index($id) {
        $nome = $descricao = NULL;

        if (!empty($id)) {
            $dados = $this->categoria->editarCategoria($id);

            if (empty($dados)) {
                $_SESSION['modalTitle'] = "Dados inválidos";
                $_SESSION['modalMessage'] = "Os dados não foram encontrados.";
                exit;
            }

            $id = $dados->id_categoria;
            $nome = $dados->nm_cat;
            $descricao = $dados->desc_cat;
        }

        require "../Views/categoria/index.php";
    }

    public function listar() {
        require "../Views/categoria/listar.php";
    }

    // salvar é ativado sozinho como rota de formulario enviado nessa url aqui do formulario de Usuario.: /usuario/salvar
    public function salvar() {
        
        // pegamos tudo o que vem do formulario
        $inputData = $_POST;
        $cleanData = $this->sanitizacao($inputData); // Pega e sanitiza o que veio do formulario de Categoria.

        /**
         * fazemos validação aqui
         */

        $validacaoFalhou = false;

        if (empty($cleanData['nm_cat'])) { // nome de usuario necessario
            /**
             * modalTitle e Message servem para dar mensagens personalizadas ao usuario com um Modal.
             * nao se preocupe com a ativação do modal, ele é ativado sozinho quando chega uma mensagem à ele.
             * ele está em Views/Components/FlashMessage.php
             */
            $_SESSION['modalTitle'] = "Nome de categoria Inválido";
            $_SESSION['modalMessage'] = "Por favor, preencha o nome da categoria.";
            $validacaoFalhou = true;
        } else if(empty($cleanData['desc_cat'])){
            $_SESSION['modalTitle'] = "Defina uma descrição para a categoria";
            $_SESSION['modalMessage'] = "A categoria foi informada sem descrição.";
            $validacaoFalhou = true;
        }

        if($validacaoFalhou){ // se validacao falhou é true ou existe, então só redirecione o usuario para /categoria
            header("Location: " . BASE_URL . "/categoria");
            exit;
        } else{
            
            // se id não existir ou estiver vazio fará insert
            if (empty($cleanData['id_categoria'])) {
                // OPERAÇÃO: INSERT
                // Senha é obrigatória no insert (validado acima)
                $mensagem = $this->categoria->inserirCategoria($cleanData);

            } else {
                // OPERAÇÃO: UPDATE
                
                $mensagem = $this->categoria->atualizarCategoria($cleanData);
            }

            if($mensagem == 1){ // operação de sucesso
                $_SESSION['modalTitle'] = "Operação realizada com sucesso.";
                $_SESSION['modalMessage'] = "Categoria salva ou atualizada.";
            } else{ // operação de erro
                $_SESSION['modalTitle'] = "Falha na Operação.";
                $_SESSION['modalMessage'] = "A categoria não foi salva ou atualizada por alguma falha interna.";
            }
            header("Location: " . BASE_URL . "/categoria/listar"); // redirecione o usuario para a pagina de listagem.
            exit;
        }
    }

    public function excluir($id) {
        if(empty($id)){
            $_SESSION['modalTitle'] = "Registro Inválido";
            $_SESSION['modalMessage'] = "O registro fornecido é inválido.";
        } else{
            $mensagem = $this->categoria->excluirCategoria($id);
            if($mensagem == 1){
                $_SESSION['modalTitle'] = "Registro excluído";
                $_SESSION['modalMessage'] = "O registro fornecido foi excluido.";
            } else{
                $_SESSION['modalTitle'] = "O Registro não foi excluído";
                $_SESSION['modalMessage'] = "O registro fornecido não foi excluido por alguma falha interna.";
            }
        }

        header("Location: " . BASE_URL . "/categoria/listar");
        exit;
    }

}
