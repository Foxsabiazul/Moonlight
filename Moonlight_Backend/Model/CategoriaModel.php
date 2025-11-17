<?php

class CategoriaModel {
    //declaração de variáveis 
    private $pdo;
    private int $id_categoriajogos;
    private int $id_biblio_user;
    private string $cat_descricao = "";
    private int $id_games;
    private string $nome_categoria = "";

    public function __construct(int $id_categoriajogos, int $id_games, int $id_biblio_user, string $cat_descricao, string $nome_categoria) 
    {//parametros
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
        
        //GETTERS
    public function getPdo() 
    {
        return $this->pdo;
    }

	public function getIdCategoriajogos(): int 
    {
        return $this->id_categoriajogos;
    }

	public function getIdBiblioUser(): int 
    {
        return $this->id_biblio_user;
    }

	public function getIdGames(): int 
    {
        return $this->id_games;
    }

    //SETTERS

	public function setPdo( $pdo): void 
    {
        $this->pdo = $pdo;
    }

	public function setIdCategoriajogos(int $id_categoriajogos): void 
    {
        $this->id_categoriajogos = $id_categoriajogos;
    }

	public function setIdBiblioUser(int $id_biblio_user): void 
    {
        $this->id_biblio_user = $id_biblio_user;
    }

	public function setIdGames(int $id_games): void 
    {
        $this->id_games = $id_games;
    }

}



