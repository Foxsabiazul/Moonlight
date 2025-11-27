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

    // Helper para converter string vazia para NULL
    function empty_to_null($value) {
        return (empty($value) && $value !== 0) ? NULL : $value;
    }

    // ATENÇÃO: Use empty_to_null() ao ler o GET
    $categoria = empty_to_null($_GET["categoria"] ?? NULL);
    $termo = empty_to_null($_GET['termo'] ?? NULL);

    // Definição com padrão, tratando strings vazias
    // Se a URL passar &order=, o empty_to_null() retorna NULL, e o ?? aplica o padrão.
    $order = empty_to_null($_GET['order'] ?? NULL) ?? 'titulo';     // Padrão: 'titulo'
    $filtro = empty_to_null($_GET['filtro'] ?? NULL) ?? 'titulo';   // Padrão: 'titulo'
    $operador = empty_to_null($_GET['operador'] ?? NULL) ?? 'LIKE'; // Padrão: 'LIKE'

    $limit = 8; // Define o limite de jogos por página
    $page = (int)($_GET["page"] ?? 1); // Pega o parâmetro 'page' (padrão é 1)
    $offset = ($page - 1) * $limit; // Calcula o OFFSET: (Página - 1) * Limite

    $pdo = Conexao::connect();

    $allowedFilters = ['titulo', 'preco', 'data_lancamento'];
    $allowedOperators = ['=', '>', '<', 'LIKE'];
    $allowedOrders = ["titulo", "preco", "data_lancamento"]; 

    // Validação da Ordenação (SQL Injection Guard)
    if (!in_array($order, $allowedOrders)) {
        $order = "titulo";
    }

    // Validação do Filtro e Operador (SQL Injection Guard)

    if (!in_array($filtro, $allowedFilters) || !in_array($operador, $allowedOperators)) {
        // Se a validação falhar, retorna um array vazio (ou erro) e encerra a execução
        echo json_encode([]);
        exit;
    }

    if (!empty($termo) || !empty($order)) {
        
        // --- LÓGICA DE BUSCA AVANÇADA (Termo não vazio) ---

        $where = "WHERE ativo = 'S'"; // Inicia a cláusula WHERE
        $binds = [];

        
        // Adicionar filtro por OUTROS CAMPOS (Título, Preço, Data)
        if($filtro) {

            if ($operador === 'LIKE') {

                // Para campos de texto (titulo)
                $where .= " AND $filtro LIKE :termo"; 
                $binds[':termo'] = "%{$termo}%";

            } else {

                // Para campos numéricos/data (preco, data_lancamento)
                $where .= " AND $filtro $operador :termo";
                // Preço/Data precisam de conversão se vierem em formato regional
                if ($filtro === 'preco') {
                    // Remove ponto de milhar e substitui vírgula por ponto para PDO
                    
                    $binds[':termo'] = $termo;
                } else {
                    $binds[':termo'] = $termo;
                }
                
            }

            // Adicionar filtro por CATEGORIA
            if ($categoria) {
                // O termo de busca é o ID da categoria
                $where .= " AND id_categoria = :id_categoria";
                $binds[':id_categoria'] = $categoria;
            } 

        }
        
        // Monta a consulta final de busca
        $sql = "SELECT * FROM jogos $where ORDER BY $order LIMIT :limit OFFSET :offset";
        $consulta = $pdo->prepare($sql);
        
        // Bind dos parâmetros de busca e paginação
        foreach ($binds as $key => $value) {
            // Se for o :termo e o filtro for 'preco', trata como float para bind.
            if ($key === ':termo' && $filtro === 'preco') {
                $consulta->bindValue($key, $value, PDO::PARAM_STR);
            } else if ($key === ':id_categoria' && $categoria) {
                $consulta->bindValue($key, (int)$value, PDO::PARAM_INT);
            } else {
                 $consulta->bindValue($key, $value);
            }
        }
        
        $consulta->bindParam(":limit", $limit, PDO::PARAM_INT);
        $consulta->bindParam(":offset", $offset, PDO::PARAM_INT);
        $consulta->execute();
        $dados = $consulta->fetchAll(PDO::FETCH_OBJ);

    } else if(!empty($categoria) && empty($termo)){

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