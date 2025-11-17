<?php
require __DIR__ . '/../../vendor/autoload.php';
use Moonlight_Backend\config\Conexao;

    header("Content-Type: application/json");

    $id = $_GET["id"] ?? NULL;

    if(empty($id)){
        $dados = array("erro"=>"ID inválido");
    } else {
        //CATEGORIA ESPECIFICA
        $pdo = Conexao::connect();

        $sql = "select * from categorias
        where id_categoria = :id_categoria
        order by nm_cat";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":id_categoria", $id);
        $consulta->execute();

        $dados = $consulta->fetchAll(PDO::FETCH_OBJ);
    }
    echo json_encode($dados);