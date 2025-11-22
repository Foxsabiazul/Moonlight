<?php
require __DIR__ . '/../../vendor/autoload.php';
use Moonlight_Backend\config\Conexao;

    header("Content-Type: application/json");

    /**
     * O principal objetivo do OFFSET é permitir a Paginação. Ele funciona em conjunto com a cláusula LIMIT.
     * 
     * O que é LIMIT?
     * O LIMIT define o número máximo de linhas que a consulta deve retornar. (Ex: "Quero no máximo 10 jogos").
     * 
     * O que é OFFSET?
     * O OFFSET define a quantidade de linhas a serem puladas a partir do início do conjunto de resultados antes de aplicar o LIMIT. (Ex: "Pule os primeiros 20 jogos").
     */

    /**
     * ⚠️ Ponto de Atenção: ORDER BY
     * É crucial que usemos o OFFSET junto com a cláusula ORDER BY (ordenar por título, ID, data, etc.).
     * Se não usar o ORDER BY (ou usar ORDER BY rand()), o banco de dados pode retornar os resultados em uma ordem inconsistente a cada consulta.
     * Isso significa que, na página 2, você pode ver jogos que já viu na página 1, quebrando a funcionalidade da paginação.
     * Portanto, em um ambiente de produção com paginação, a consulta deve sempre ser determinística:
     *
     * SELECT * FROM jogos 
     * WHERE ativo = 'S' ORDER BY titulo ASC -- ESSENCIAL para garantir a ordem
     * LIMIT 8 OFFSET 8;
     */

    $categoria = $_GET["categoria"] ?? NULL;
    $limit = 8; // Define o limite de jogos por página
    $page = (int)($_GET["page"] ?? 1); // Pega o parâmetro 'page' (padrão é 1)
    $offset = ($page - 1) * $limit; // Calcula o OFFSET: (Página - 1) * Limite

    $pdo = Conexao::connect();

    if(!empty($categoria)){

        //JOGOS DE UMA CATEGORIA ESPECIFICA
        $sql = "select j.*, c.nm_cat from jogos j INNER JOIN categorias c ON j.id_categoria = c.id_categoria where j.ativo = 'S' and j.id_categoria = :id_categoria order by j.titulo limit :limit offset :offset";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":id_categoria", $categoria);
        $consulta->bindParam(":limit", $limit, PDO::PARAM_INT);
        $consulta->bindParam(":offset", $offset, PDO::PARAM_INT);
        $consulta->execute();
        $dados = $consulta->fetchAll(PDO::FETCH_OBJ);
    } else{
        // TODOS OS JOGOS
        $sql = "SELECT * FROM jogos 
            WHERE ativo = 'S'";
        $sql .= " ORDER BY id_games ASC 
              LIMIT :limit OFFSET :offset";

        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":limit", $limit, PDO::PARAM_INT);
        $consulta->bindParam(":offset", $offset, PDO::PARAM_INT);
        $consulta->execute();
        $dados = $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    //mostrar em json
    echo json_encode($dados);