<?php

class BibliotecaModel{
    private int $id;
    private int $idUser;
    private float $data;

    public function __construct(int $id, int $idUser, float $data)
    {
        $this->id = $id;
        $this->idUser = $idUser;
        $this->data = $data;
    }
    
    public function getId(): int 
    {
        return $this->id;
    }

	public function getIdUser(): int 
    {
        return $this->idUser;
    }

	public function getData(): float 
    {
        return $this->data;
    }

    public function setId(int $id): void 
    {
        $this->id = $id;
    }

	public function setIdUser(int $idUser): void 
    {
        $this->idUser = $idUser;
    }

	public function setData(float $data): void
    {
        $this->data = $data;
    }

	

	
}