<?php

namespace Moonlight_Backend\Model;
use PDO;

class GamesModel {
    //declaração de variáveis 
    private PDO $pdo;

    public function __construct(
        PDO $pdo,
    )
    {
        $this->pdo = $pdo;
    }

    /**
     * @param array $dados Os dados do Jogo.
     * @return bool resposta ao usuario.
     */
    public function inserirGames(array $dados): bool {
        // Colunas: id_categoria, titulo, preco, data_lancamento, ativo (os que são obrigatorios entrarem). Os que não são, estão no isset abaixo:

        // insert exige que as colunas sejam preenchidas desta forma, ao invés de fazer tudo em seguida igual ao update que faz assim por ex: 'id_categoria = :id_categoria'
        $columns = [
            'id_categoria', 
            'titulo', 
            'preco',
            'data_lancamento', 
            'ativo'
        ];

        $parameters = [
            ":id_categoria" => $dados['id_categoria'],
            ":titulo"       => $dados['titulo'],
            ":preco"        => $dados['preco'],
            ":data_lancamento" => $dados['data_lancamento'],
            ":ativo"        => $dados['ativo']
        ];

        // colunas opcionais
        if (isset($dados['descricao'])) { 
            $columns[] = 'descricao'; // Adiciona o nome da coluna dentro do array como uma key. ex: $columns['descricao'] (mas nao define valor)
            $parameters[":descricao"] = $dados['descricao']; // Adiciona placeholder (:descricao) como uma key do array e seu devido valor para realizar o bindParam em foreach no final.
        }

        if (isset($dados['imagem'])){
            $columns[] = 'imagem';
            $parameters[":imagem"] = $dados['imagem'];
        }

        if(isset($dados['link'])){
            $columns[] = 'link';
            $parameters[":link"] = $dados['link'];
        }

        // pegamos as keys dos arrays (o apelido da "variavel" dentro do array) e transformamos em uma string normal.
        $columnsList = implode(', ', $columns); // "id_categoria, titulo, preco, ..."
        $parametersList = implode(', ', array_keys($parameters)); // ":id_categoria, :titulo, :preco, ..."

        $sql = "INSERT INTO jogos (
                    {$columnsList}
                ) 
                VALUES (
                    {$parametersList}
                )";
        
        $consulta = $this->pdo->prepare($sql);
        
        foreach ($parameters as $key => $value) {
            $consulta->bindParam($key, $parameters[$key]);
        }

        return $consulta->execute();
    }

    public function inserirGamesVazios(int $valor) {
        $sql = "CALL sp_inserir_jogos_massa(:valor)";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":valor", $valor, PDO::PARAM_INT);
        $consulta->execute();
    }

    /**
     * @param array $dados Os dados do Jogo.
     * @return bool resposta ao usuario
     * * Faz update na tabela Jogos, com base nos dados fornecidos da controller
     */
    public function atualizarGames(array $dados): bool {

        // Todas as colunas que podem ser alteradas (exceto a chave primária id_games)

        // o update exige que as colunas sejam chamadas assim no comando. 
        $setClauses = "
            id_categoria = :id_categoria, 
            titulo = :titulo, 
            preco = :preco,
            data_lancamento = :data_lancamento, 
            ativo = :ativo
        ";

        $parameters = [
            ":id_categoria" => $dados['id_categoria'],
            ":titulo"       => $dados['titulo'],
            ":preco"        => $dados['preco'],
            ":data_lancamento" => $dados['data_lancamento'],
            ":ativo"        => $dados['ativo'],
            ":id_games" => $dados['id_games']
        ];

        if (isset($dados['descricao'])) { 
            $setClauses .= ", descricao = :descricao"; //mesma logica do de cima (queremos fazer uma string), a diferença é que ja adicionamos na string antes: ex: $setClauses = $setClauses + ", descricao = :descricao";
            $parameters[":descricao"] = $dados['descricao'];
        }

        if (isset($dados['imagem'])){
            $setClauses .= ", imagem = :imagem";
            $parameters[":imagem"] = $dados['imagem'];
        }

        if(isset($dados['link'])){
            $setClauses .= ", link = :link";
            $parameters[":link"] = $dados['link'];
        }
        
        // Coluna `data_lancamento` é do tipo DATE (Y-m-d)
        // Coluna `preco` é DECIMAL(10, 2)
        // Coluna `ativo` é ENUM('S', 'N')

        //auditoria

        // Obtenha o nome de usuário da sua sessão PHP
        $usuarioLogado = $_SESSION['Logado_Na_Sessão']['nm_user'] ?? 'Sistema Desconhecido';

        // Prepare e execute o comando SET @usuario_logado

        $sqlSetUser = "SET @usuario_logado = :usuario_logado";
        $stmtSetUser = $this->pdo->prepare($sqlSetUser);
        $stmtSetUser->bindParam(":usuario_logado", $usuarioLogado);
        $stmtSetUser->execute();

        $sql = "UPDATE jogos SET {$setClauses} WHERE id_games = :id_games LIMIT 1";
        $consulta = $this->pdo->prepare($sql);
        
        // Faz o bind de todos os parâmetros dinamicamente
        foreach ($parameters as $key => $value) {
            $consulta->bindParam($key, $parameters[$key]);
        }

        return $consulta->execute();
    }

    public function listarGames() {
        // Ajustado para ordenar por título e traz o nome da categoria da propria tabela dela direto.
        $sql = "SELECT j.*, c.nm_cat 
                FROM jogos j
                INNER JOIN categorias c ON j.id_categoria = c.id_categoria
                ORDER BY j.titulo"; 
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lista apenas os jogos que estão 'Prontos para Venda', usando a função SQL.
     * @return array|null Dados dos jogos.
     */
    public function listarGamesAtivosApenas() {
        // [MODIFICAÇÃO] Usa a função SQL na cláusula WHERE para filtrar.
        $sql = "SELECT 
                    j.*, 
                    c.nm_cat
                FROM jogos j
                INNER JOIN categorias c ON j.id_categoria = c.id_categoria
                WHERE fn_verificar_jogo_pronto_venda(j.id_games) = TRUE
                ORDER BY j.titulo";
        
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    // editar não é update, ele serve para resgatar os valores do banco para trazer à interface do formulario de Jogo
    public function editarGames($id) {
        $sql = "SELECT * FROM jogos WHERE id_games = :id_games LIMIT 1";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":id_games", $id);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_OBJ);
    }

    public function excluirGames($id) {
        try {
            // ... (Lógica para excluir auditoriapreco com CASCADE ou a própria AuditoriaPreco já tem a regra)

            // Tenta excluir o jogo principal (aqui o RESTRICT vai bloquear)
            $sql = "DELETE FROM jogos WHERE id_games = :id_games LIMIT 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id_games", $id);

            $consulta->execute();
            
            return true; // Sucesso na exclusão
            
        } catch (\PDOException $e) {
            

            // Verifica se o erro é de Chave Estrangeira (23000 ou código 1451 no MySQL)
            if ($e->getCode() == '23000' || str_contains($e->getMessage(), '1451')) {
                $fk_error_message = "Este jogo não pode ser excluído, pois já foi vendido e faz parte do histórico de pedidos.";
                return $fk_error_message; // Retorna a mensagem amigável
            }
            
            // Retorna o erro inesperado
            return "Ocorreu um erro inesperado ao tentar excluir o jogo. Detalhes: " . $e->getMessage();
        }
    }
}