<?php

require "./Moonlight_Backend/Model/CategoriaModel.php";

class CategoriaController{
    private int $id = 0;
    private string $descricao = ""; //vetor/array
    private string $nome; //nome da categoria
    private $pdo;
    
       public function  __construct(int $id, float $pdo, string $descricao, string $nome)//parametros 
       {
            $this->id = $id;
            $this->descricao = $descricao;
            $this->nome = $nome;
       }

       public function listar(string $nome, string $descricao, int $id){//função de listagem/filtragem por descrição __ talvez mude para nome  
           $this->nome = $nome;

        if($id >= $descricao){
            echo("descrição salva!");
        }else{
            return "problema com a descrição";
        }
    }
        public function excluir($id) {
            $sql = "delete from categoria where id = :id limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id", $id);

            return $consulta->execute();
        }

        public function salvar(){
            if(empty( $__POST["id"])){
                $sql = "insert into categoria (id, descricao, ativo) values (NULL, :descricao, :ativo)";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":descricao",$_POST["descricao"]);
                $consulta->bindParam(":ativo", $_POST["ativo"]);
            }else{
                $sql = "update categoria set descricao = :descricao, ativo where id = :id limit 1";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":descricao", $_POST["descricao"]);
                $consulta->bindParam(":ativo", $_POST["ativo"]);
                $consulta->bindParam(":id", $_POST["id"]);
            }

            return $consulta->execute();
        }

    }
