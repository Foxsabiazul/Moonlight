<?php

class FavoritosModel{
    private int $id;
    private string $fav_star;
    private $pdo;

    //parametros 
    public function __construct($pdo, int $id, string $fav_star) 
    {
        $this->id = $id;
        $this->fav_star = $fav_star;  
        $this->pdo = $pdo;  
    }

    public function getId(): int 
    {
        return $this->id;
    }

    public function getFav_Star(): string 
    {
        return $this->fav_star;
    }

    public function setId(int $id): void 
    {
        $this->id = $id;
    }

    public function setFav_Star(string $fav_star): void
    {
        $this->fav_star = $fav_star;
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

}


?>