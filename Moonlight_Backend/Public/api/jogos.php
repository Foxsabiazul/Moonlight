<?php
require __DIR__ . '/../../vendor/autoload.php';
use Moonlight_Backend\config\Conexao;

    header("Content-Type: application/json");

    $id = $_GET["id"] ?? NULL;
    $categoria = $_GET["categoria"] ?? NULL;

    $pdo = Conexao::connect();

    if(!empty($categoria)){
        //JOGOS DE UMA CATEGORIA ESPECIFICA
        $sql = "select * from jogos where ativo = 'S' and id_categoria = :id_categoria order by titulo";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":id_categoria", $categoria);
        $consulta->execute();
        $dados = $consulta->fetchAll(PDO::FETCH_OBJ);
    } else{
        // TODOS OS JOGOS
        $sql = "select * from jogos where ativo = 'S'
        order by rand()
        limit 8";

        $consulta = $pdo->prepare($sql);
        $consulta->execute();
        $dados = $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    //mostrar em json
    echo json_encode($dados);