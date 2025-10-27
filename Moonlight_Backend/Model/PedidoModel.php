<?php

class PedidoModel{
    private int $idOrder;
    private float $dataPED;
    private float $total;
    private float $preco;
    private string $itens;

    public function __construct(int $idOrder, float $dataPED, float $total, float $preco, string $itens)
    {
        $this->idOrder = $idOrder;
        $this->dataPED = $dataPED;
        $this->total = $total;
        $this->preco = $preco;
        $this->itens = $itens;
    }

    public function getIdOrder(): int 
    {
        return $this->idOrder;
    }

	public function getDataPED(): float 
    {
        return $this->dataPED;
    }

	public function getTotal(): float 
    {
        return $this->total;
    }

	public function getPreco(): float 
    {
        return $this->preco;
    }

	public function getItens(): string 
    {
        return $this->itens;
    }

    public function setIdOrder(int $idOrder): void 
    {
        $this->idOrder = $idOrder;
    }

	public function setDataPED(float $dataPED): void 
    {
        $this->dataPED = $dataPED;
    }

	public function setTotal(float $total): void 
    {
        $this->total = $total;
    }

	public function setPreco(float $preco): void 
    {
        $this->preco = $preco;
    }

	public function setItens(string $itens): void
    {
        $this->itens = $itens;
    }

}


?>