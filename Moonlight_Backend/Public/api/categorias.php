<?php
    require __DIR__ . '/../../vendor/autoload.php';
    use Moonlight_Backend\config\Conexao;       

    header("Content-Type: application/json");

    $pdo = Conexao::connect();

    //QUALQUER CATEGORIA.
    $sql = "select * from categorias
    order by nm_cat";

    $consulta = $pdo->prepare($sql);
    $consulta->execute();
    $dados = $consulta->fetchAll(PDO::FETCH_OBJ);

    //mostrar em json
    echo json_encode($dados);