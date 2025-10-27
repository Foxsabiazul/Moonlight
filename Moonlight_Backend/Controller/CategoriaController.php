<?php


class CategoriaController{
    private int $id = 0;
    private string $descricao = ""; //vetor/array
    private string $nome; //nome da categoria
    
       public function  __construct(int $id, string $descricao, string $nome)//parametros 
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
}