<?php

class CategoriaModel {
    
    private $pdo;
    private static $id_categoriajogos;
    private $id_biblio_user;
    private $cat_descricao = "";
    private $id_games;
    private $nome_categoria = "";

    public function __construct(int $id_categoriajogos, int $id_games, int $id_biblio_user, string $cat_descricao, string $nome_categoria) {

        $this->id_categoriajogos = $id_categoriajogos;
        $this->id_biblio_user = $id_biblio_user;
        $this->id_games = $id_games;
        $this->cat_descricao = $cat_descricao;
        $this->nome_categoria = $nome_categoria;

    }
          public function Listar() {
            $sql = "select * from categoria order by descricao";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }
}



