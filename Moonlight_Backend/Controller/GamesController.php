<?php

namespace Moonlight_Backend\Controller;

use Moonlight_Backend\config\Sanitizador;
use Moonlight_Backend\config\Conexao;
use Moonlight_Backend\Model\CategoriaModel;
use Moonlight_Backend\Model\GamesModel;

class GamesController{

    private $games;
    private $categoria;

    public function __construct(){
        // para fazer conexão singleton
        $pdo = Conexao::connect();
        $this->categoria = new CategoriaModel($pdo);
        $this->games = new GamesModel($pdo);
    }

    // metódo de sanitização para limpar os espaços vazios que vem de formulario.
    public function sanitizacao(array $inputData): array
    {
        $id_games = Sanitizador::sanitizar($inputData["id_games"] ?? '');
        $id_categoria = Sanitizador::sanitizar($inputData["id_categoria"] ?? '');
        $titulo = Sanitizador::sanitizar($inputData["titulo"] ?? '');
        $descricao = Sanitizador::sanitizar($inputData["descricao"] ?? '');
        $preco = Sanitizador::sanitizar($inputData["preco"] ?? '');
        $link = Sanitizador::sanitizar($inputData["link"] ?? '');
        $ativo = Sanitizador::sanitizar($inputData["ativo"] ?? '');

        return[
            "id_games" => $id_games,
            "id_categoria" => $id_categoria,
            "titulo" => $titulo,
            "descricao" => $descricao,
            "preco" => $preco,
            "link" => $link,
            "ativo" => $ativo
        ];

    }

    // a index é pagina de cadastro ou seja é um (insert ou update) então por isso tem o $id de parametração.
    // o id serve pra receber o que vêm da URL.
    /**
     * O Papel do Parâmetro $id
     * O parâmetro $id no GamesController index($id) vem da URL (geralmente como o terceiro segmento, ex: /games/index/5).
     * Se $id está vazio/nulo: A URL é do tipo /games ou /games/index. O fluxo é de Inserção (Novo Cadastro). Aí inicializamos as variáveis $nome, $email, etc., como NULL ou vazias, e o formulário é carregado em branco.
     * Se $id tem um valor (ex: 5): A URL é do tipo /games/index/5. O fluxo é de Atualização (Edição).
     */
    public function index($id_games) {
        $id_categoria = $titulo = $descricao = $preco = $link = $data = $ativo = NULL;

        if (!empty($id_games)) { // a condição serve pra verificar se o admin quer editar as informações desse registro. E se o id não estiver vazio na URL /games/index/{O ID VEM AQUI} então cai na condição.
            $dados = $this->games->editarGames($id_games);
            // puxa os dados que forem necessarios com base no id da url (performance).

            if (empty($dados)) { //se não encontrar, é invalido e ja sai da logica.
                $_SESSION['modalTitle'] = "Dados inválidos";
                $_SESSION['modalMessage'] = "Os dados não foram encontrados.";
                header("Location: " . BASE_URL . "/games/listar");
                exit;
            }

            // aqui são as informações que serão puxadas prontas.
            $id_games = $dados->id_games;
            $id_categoria = $dados->id_categoria;
            $titulo = $dados->titulo;
            $descricao = $dados->descricao;
            $preco = $dados->preco;
            $link = $dados->link;
            $data = $dados->data_lancamento;
            $ativo = $dados->ativo;
        }

        require "../Views/games/index.php";
    }

    public function listar() {
        require "../Views/games/listar.php";
    }

    // salvar é ativado sozinho como rota de formulario enviado nessa url aqui do formulario de Games.: /games/salvar
    public function salvar() {
        
        // pegamos tudo o que vem do formulario
        $inputData = $_POST;
        $cleanData = $this->sanitizacao($inputData); // Pega e sanitiza o que veio do formulario de Games.


        if (!empty($_FILES["imagem"]["name"])) { //O bloco só é executado se o campo imagem no formulário tiver um nome de arquivo (ou seja, se um arquivo foi enviado).
            $arquivo = time() . ".jpg"; //Cria um nome único para o arquivo. Usa a função time() (que retorna o timestamp atual em segundos) para evitar colisões de nomes e anexa a extensão .jpg
            if(!move_uploaded_file($_FILES["imagem"]["tmp_name"], "arquivos/{$arquivo}")) { //Tenta mover o arquivo da pasta temporária do servidor ($_FILES["imagem"]["tmp_name"]) para (o diretório arquivos/ com o novo nome único), se falhar cai na condição
                $_SESSION['modalTitle'] = "Erro no arquivo";
                $_SESSION['modalMessage'] = "Erro ao copiar arquivo.";
                exit;
            }
            $cleanData["imagem"] = $arquivo; // armazenamos o nome da imagem pro banco de dados.
        }

        $cleanData["data_lancamento"] = $inputData["data_lancamento"];

        //formar o preco 1.600,90 -> 1600,90 -> 1600.90
        $cleanData["preco"] = str_replace(".", "", $cleanData["preco"]);
        $cleanData["preco"] = str_replace(",", ".", $cleanData["preco"]);

        /**
         * fazemos validação aqui
         */

        $acceptedCases = ["S", "N"];
        $validacaoFalhou = false;

        if (empty($cleanData['id_categoria'])){
            /**
             * modalTitle e Message servem para dar mensagens personalizadas ao usuario com um Modal.
             * nao se preocupe com a ativação do modal, ele é ativado sozinho quando chega uma mensagem à ele.
             * ele está em Views/Components/FlashMessage.php
             */
            $_SESSION['modalTitle'] = "Categoria Inválida";
            $_SESSION['modalMessage'] = "Por favor, preencha a categoria do jogo.";
            $validacaoFalhou = true;
        } else if(empty($cleanData['titulo'])) {
            $_SESSION['modalTitle'] = "Titulo Inválido";
            $_SESSION['modalMessage'] = "Por favor, preencha o titulo.";
            $validacaoFalhou = true;
        } else if(empty($cleanData['preco'])){
            $_SESSION['modalTitle'] = "Preço inválido";
            $_SESSION['modalMessage'] = "O preço não foi informado.";
            $validacaoFalhou = true;
        } else if(empty($cleanData['data_lancamento'])){
            $_SESSION['modalTitle'] = "Data de lançamento inválida";
            $_SESSION['modalMessage'] = "A data de lançamento do jogo não foi informada.";
            $validacaoFalhou = true;
        } else if(!in_array($cleanData['ativo'], $acceptedCases, true)){ 
            // inArray(valor que queremos verificar, valores predefinidos, Sendo true, a função verifica se o valor e o tipo de dado são idênticos)
            // o "!" inverte e significa "se não tiver os valores predefinidos ou se o valor e tipo de dados sao diferentes, entra no caso".
            $_SESSION['modalTitle'] = "Valor 'Ativo' Inválido";
            $_SESSION['modalMessage'] = "O campo 'Ativo' deve ser 'S' ou 'N'.";
            $validacaoFalhou = true;
        }
        

        if($validacaoFalhou){ // se validacao falhou é true ou existe, então só redirecione o usuario para /Games
            header("Location: " . BASE_URL . "/games");
            exit;
        } else{
            
            // se id não existir ou estiver vazio fará insert
            if (empty($cleanData['id_games'])) {
                // OPERAÇÃO: INSERT

                $mensagem = $this->games->inserirGames($cleanData);

            } else {
                // OPERAÇÃO: UPDATE
                
                $mensagem = $this->games->atualizarGames($cleanData);
            }

            if($mensagem == 1){ // operação de sucesso
                $_SESSION['modalTitle'] = "Operação realizada com sucesso.";
                $_SESSION['modalMessage'] = "Jogo salvo ou atualizado.";
            } else{ // operação de erro
                $_SESSION['modalTitle'] = "Falha na Operação.";
                $_SESSION['modalMessage'] = "O Jogo não foi salvo ou atualizado por alguma falha interna.";
            }
            header("Location: " . BASE_URL . "/games/listar"); // redirecione o usuario para a pagina de listagem.
            exit;
        }
    }

    public function excluir($id) {
        if(empty($id)){
            $_SESSION['modalTitle'] = "Registro Inválido";
            $_SESSION['modalMessage'] = "O registro fornecido é inválido.";
        } else{
            $mensagem = $this->games->excluirGames($id);
            if($mensagem == 1){
                $_SESSION['modalTitle'] = "Registro excluído";
                $_SESSION['modalMessage'] = "O registro fornecido foi excluido.";
            } else{
                $_SESSION['modalTitle'] = "O Registro não foi excluído";
                $_SESSION['modalMessage'] = "O registro fornecido não foi excluido por alguma falha interna.";
            }
        }

        header("Location: " . BASE_URL . "/games/listar");
        exit;
    }

}
