<?php

class FavoritosModel{
    private int $id;
    private string $fav_star;

    //parametros 
    public function __construct(int $id, string $fav_star) 
    {
        $this->id = $id;
        $this->fav_star = $fav_star;    
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
	
}


?>