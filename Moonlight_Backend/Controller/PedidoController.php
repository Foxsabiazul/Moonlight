<?php

require "./Moonlight_Backend/Model/PedidoModel.php";

class PedidoController {
private int $id;
private float $compra;
private string $itens;
private float $preco;
private float $total;
private $pdo;

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