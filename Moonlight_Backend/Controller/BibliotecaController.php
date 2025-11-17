<?php

require "./Moonlight_Backend/Model/BibliotecaModel.php";

class BibliotecaController {
  private int $id_games;
  private string $categoria;
  private $pdo;

  public function _construct(int $id_games, string $categoria, $pdo){
    $this->id_games = $id_games;
    $this->categoria = $categoria;
    $this->pdo = $pdo;
  }


  public function listar() {
            $sql = "select * from usuários order by nm_user";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }
}