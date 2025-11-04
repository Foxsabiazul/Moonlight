<?php

require "./Moonlight_Backend/Model/GamesModel.php";

class GamesController {
    private $pdo;
    private $id;

     public function __construct($pdo, $id) 
        {
            $this->pdo = $pdo;
        }

        public function editar($id) {
            $sql = "select * from produto where id = :id limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id", $id); 
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_OBJ);
        }

        public function listarCategoria() {
            $sql = "select * from categoria order by descricao";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }

        public function salvar() {
    
            if (empty($_POST["id"])) {
                $sql = "insert into produto (id, nome, categoria_id, descricao, imagem, valor, destaque, ativo) 
                values (NULL, :nome, :categoria_id, :descricao, :imagem, :valor, :destaque, :ativo)";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":nome", $_POST["nome"]);
                $consulta->bindParam(":categoria_id", $_POST["categoria_id"]);
                $consulta->bindParam(":descricao", $_POST["descricao"]);
                $consulta->bindParam(":imagem", $_POST["imagem"]);
                $consulta->bindParam(":valor", $_POST["valor"]);
                $consulta->bindParam(":destaque", $_POST["destaque"]);
                $consulta->bindParam(":ativo", $_POST["ativo"]);
            } else {
                $sql = "update produto set nome = :nome, categoria_id = :categoria_id, descricao = :descricao, imagem = :imagem, valor = :valor, 
                    destaque = :destaque, ativo = :ativo where id = :id limit 1";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":nome", $_POST["nome"]);
                $consulta->bindParam(":categoria_id", $_POST["categoria_id"]);
                $consulta->bindParam(":descricao", $_POST["descricao"]);
                $consulta->bindParam(":imagem", $_POST["imagem"]);
                $consulta->bindParam(":valor", $_POST["valor"]);
                $consulta->bindParam(":destaque", $_POST["destaque"]);
                $consulta->bindParam(":ativo", $_POST["ativo"]);
                $consulta->bindParam(":id", $_POST["id"]);
            }

            return $consulta->execute();
        }

        public function excluir($id) {
            $sql = "delete from produto where id = :id limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id", $id);

            return $consulta->execute();
        }

}