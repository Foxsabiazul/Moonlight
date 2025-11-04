<?php

class GamesModel {
    private int $id;
    private string $titulo;
    private string $descricao;
    private string $link;
    private float $preco;
    private float $imagem;
    private float $dataLanc;

    public function __construct(int $id, string $titulo, string $descricao, string $link,
    float $preco, float $imagem, float $dataLanc) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->link = $link;
        $this->preco = $preco;
        $this->imagem = $imagem;
        $this->dataLanc = $dataLanc;
        $this->descricao = $descricao;
    }
    //GETTERS
    public function getId(): int 
    {
        return $this->id;
    }

	public function getTitulo(): string 
    {
        return $this->titulo;
    }

	public function getDescricao(): string 
    {
        return $this->descricao;
    }

	public function getLink(): string 
    {
        return $this->link;
    }

	public function getPreco(): float 
    {
        return $this->preco;
    }

	public function getImagem(): float 
    {
        return $this->imagem;
    }

	public function getDataLanc(): float 
    {
        return $this->dataLanc;
    }

    //SETTERS

    public function setId(int $id): void
    {
        $this->id = $id;
    }

	public function setTitulo(string $titulo): void 
    {
        $this->titulo = $titulo;
    }

	public function setDescricao(string $descricao): void 
    {
        $this->descricao = $descricao;
    }

	public function setLink(string $link): void 
    {
        $this->link = $link;
    } 

	public function setPreco(float $preco): void 
    {
        $this->preco = $preco;
    }

	public function setImagem(float $imagem): void 
    {
        $this->imagem = $imagem;
    }

	public function setDataLanc(float $dataLanc): void 
    {
        $this->dataLanc = $dataLanc;
    }

}


?>