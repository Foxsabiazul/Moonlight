<?php

class PedidoModel{
    private int $idOrder;
    private float $dataPED;
    private float $total;
    private float $preco;
    private string $itens;
    private $pdo;

    public function __construct($pdo, int $idOrder, float $dataPED, float $total, float $preco, string $itens)
    {
        $this->pdo = $pdo;
        $this->idOrder = $idOrder;
        $this->dataPED = $dataPED;
        $this->total = $total;
        $this->preco = $preco;
        $this->itens = $itens;
    }
    
    //GETTERS
    public function getPdo(){
        return $this->pdo;
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

    //SETTERS
    public function setPdo($pdo){
        $this->pdo = $pdo;
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

    public function salvar($dados) {
      
            if(empty($dados["id"])) {
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

public function listar() {
            $sql = "select * from produto order by nome";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }

        public function getPedido($id) {
            $sql = "select * from produto where id = :id limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id", $id);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_OBJ);
        }

        public function getDados($id) {
            $sql = "select produto_id from item where produto_id = :id limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id", $id);
            $consulta->execute();

            $dados = $consulta->fetch(PDO::FETCH_OBJ);
        }

        public function excluir($id) {
            
            $sql = "delete from produto where id = :id limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id", $id);

            return $consulta->execute();
        }

}

?>