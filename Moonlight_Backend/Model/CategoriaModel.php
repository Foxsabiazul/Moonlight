<?php

class CategoriaModel {
    //declaração de variáveis 
    private $pdo;
    private static $id_categoriajogos;
    private int $id_biblio_user;
    private string $cat_descricao = "";
    private int $id_games;
    private string $nome_categoria = "";

    public function __construct(int $id_categoriajogos, int $id_games, int $id_biblio_user, string $cat_descricao, string $nome_categoria) {//parametros
        //validação dos parametros na construção
        $this->id_categoriajogos = $id_categoriajogos;
        $this->id_biblio_user = $id_biblio_user;
        $this->id_games = $id_games;
        $this->cat_descricao = $cat_descricao;
        $this->nome_categoria = $nome_categoria;

    }
          public function Listar() {//listagem por descrição
            $sql = "select * from categoria order by descricao";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);//return com fecth para resultados 
        }
}



