<?php
require __DIR__ . '/../../vendor/autoload.php';
use Moonlight_Backend\config\Conexao;

    header("Content-Type: application/json");

    $id = $_GET["id"] ?? NULL;

    if(empty($id)){
        $dados = array("erro"=>"ID inválido");
    } else {
        //UM DETERMINADO JOGO
        $pdo = Conexao::connect();

        $sql = "select * from jogos
        where id_games = :id_games
        and ativo = 'S' limit 1";
        $consulta = $pdo->prepare($sql);
        $consulta->bindParam(":id_games", $id);
        $consulta->execute();

        $dados = $consulta->fetch(PDO::FETCH_OBJ);
    }
    echo json_encode($dados);