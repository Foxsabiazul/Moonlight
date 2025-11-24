<?php

    namespace Moonlight\Model;

use Exception;
use Moonlight\Config\Logger;
use Moonlight\Config\ModalMessage;
use PDO;
use PDOException;

class CarrinhoModel {
    private PDO $pdo;
    
    public function __construct(
        PDO $pdo,
    )
    {
        $this->pdo = $pdo;
    }

    public function salvarPedido($dataHoraAtual, $valorTotal, $status, $preference_id){

        $sqlPedido = "insert into pedidos (id_user, data_pedido, total, status, preference_id) values (:id_user, :data_pedido, :total, :status, :preference_id)";
        $consulta = $this->pdo->prepare($sqlPedido);
        $consulta->bindParam(":id_user", $_SESSION["Logado_Na_Sessão"]["id_user"], PDO::PARAM_INT);
        $consulta->bindParam(":data_pedido", $dataHoraAtual);
        $consulta->bindParam(":total", $valorTotal);
        $consulta->bindParam(":status", $status);
        $consulta->bindParam(":preference_id", $preference_id);
        
        try {

            if ($consulta->execute()) {
                // Sucesso: Retorna o ID
                $id_pedido = (int)$this->pdo->lastInsertId();

                foreach($_SESSION['carrinho'] as $itens){

                    $sqlItem = "insert into itenscompra values (:id_pedido, :id_games, :preco)";
                    $consultaItem = $this->pdo->prepare($sqlItem);
                    $consultaItem->bindParam(":id_pedido", $id_pedido, PDO::PARAM_INT);
                    $consultaItem->bindParam(":id_games", $itens["id_games"], PDO::PARAM_INT);
                    $consultaItem->bindParam(":preco", $itens["preco"], PDO::PARAM_STR);

                    if(!$consultaItem->execute()){
                        throw new \Exception("Falha desconhecida ao salvar seus itens da compra.");
                    }

                }

            } else{
                // Se execute falhou sem lançar exceção (raro no PDO), lançamos uma exceção genérica
                throw new \Exception("Falha desconhecida ao salvar seu pedido.");
            }

            unset($_SESSION['carrinho']);

        } catch (\PDOException $e) {

            // **LANÇA EXCEÇÃO DE BANCO DE DADOS**
            // Propaga a PDOException para que o Controller a capture e logue.
            Logger::logError($e, "INSERT_DB_ERROR");
            throw $e;
        }
    }
}