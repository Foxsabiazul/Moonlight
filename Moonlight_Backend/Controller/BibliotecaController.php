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

public function salvar($dados) {
    //se o id esta vazio - insert
    //se o id nao estiver vazio, imagem nao for vazia - update
    //imagem for vazia - update, mas sem a imagem
if(empty($dados["id"])) {
    // insert
    $sql = "insert into produto (nome, categoria_id, descricao, imagem, valor, ativo)
    values (:nome, :categoria_id, :descricao, :imagem, :valor, :ativo)";
    $consulta = $this->pdo->prepare($sql);
    $consulta->bindParam(":nome", $dados["nome"]);
    $consulta->bindParam(":categoria_id", $dados["categoria_id"]);
    $consulta->bindParam(":descricao", $dados["descricao"]);
    $consulta->bindParam(":imagem", $dados["imagem"]);
    $consulta->bindParam(":valor", $dados["valor"]);
    $consulta->bindParam(":ativo", $dados["ativo"]);
} else if(!empty($dados["imagem"])) {
    //update com a imagem
    $sql = "update produto set nome = :nome, categoria_id = :categoria_id,
        descricao = :descricao, imagem = :imagem, valor = :valor,
        ativo = :ativo where id = :id limit 1";
    $consulta = $this->pdo->prepare($sql);
    $consulta->bindParam(":nome", $dados["nome"]);
    $consulta->bindParam(":categoria_id", $dados["categoria_id"]);
    $consulta->bindParam(":descricao", $dados["descricao"]);
    $consulta->bindParam(":imagem", $dados["imagem"]);
    $consulta->bindParam(":valor", $dados["valor"]);
    $consulta->bindParam(":ativo", $dados["ativo"]);
    $consulta->bindParam(":id", $dados["id"]);
} else {
        //update sem a imagem
  $sql = "update produto set nome = :nome, categoria_id = :categoria_id,
      descricao = :descricao, valor = :valor,
      ativo = :ativo where id = :id limit 1";
    $consulta = $this->pdo->prepare($sql);
    $consulta->bindParam(":nome", $dados["nome"]);
    $consulta->bindParam(":categoria_id", $dados["categoria_id"]);
    $consulta->bindParam(":descricao", $dados["descricao"]);
    $consulta->bindParam(":valor", $dados["valor"]);
    $consulta->bindParam(":ativo", $dados["ativo"]);
    $consulta->bindParam(":id", $dados["id"]);
}

return $consulta->execute();
}

  public function excluir($id) {
    if(empty($id)){
      $_SESSION['modalTitle'] = "Registro Inválido";
      $_SESSION['modalMessage'] = "O registro fornecido é inválido.";
    } else{
      $mensagem = $this->$id->excluir($id);
    if($mensagem == 1){
      $_SESSION['modalTitle'] = "Registro excluído";
      $_SESSION['modalMessage'] = "O registro fornecido foi excluido.";
    } else{
      $_SESSION['modalTitle'] = "O Registro não foi excluído";
      $_SESSION['modalMessage'] = "O registro fornecido não foi excluido por alguma falha interna.";
      }
    }   
  }

  public function listar() {
            $sql = "select * from usuários order by nm_user";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }
}