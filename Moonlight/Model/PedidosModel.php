<?php

    namespace Moonlight\Model;

use Exception;
use Moonlight\Config\Logger;
use Moonlight\Config\ModalMessage;
use PDO;
use PDOException;

class PedidosModel {
    private PDO $pdo;
    
    public function __construct(
        PDO $pdo,
    )
    {
        $this->pdo = $pdo;
    }

    public function listarPedidos() {
        $sql = "select *, date_format(data_pedido, '%d/%m/%Y %H:%i') dt from pedidos where id_user = :id_user order by data_pedido";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":id_user", $_SESSION['Logado_Na_Sessão']["id_user"]);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public function listarItens($id_pedido) {
        $sql = "select j.titulo, i.preco from itenscompra i
        inner join jogos j on (j.id_games = i.id_games)
        where i.id_pedido = :pedido
        order by j.titulo";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":pedido", $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public function atualizarStatusPedidoPorExternalReference(string $external_reference, string $status){
        
        $statusPermitidos = ['pendente', 'aprovado', 'reembolsado', 'cancelado'];

        if (!in_array($status, $statusPermitidos)) {
            // Lançar exceção ou logar erro se o status for inválido
            throw new \Exception("Status de pedido inválido fornecido: " . $status);
        }

        $sql = "UPDATE pedidos SET status = :status WHERE external_reference = :external_reference";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":status", $status);
        $consulta->bindParam(":external_reference", $external_reference);

        return $consulta->execute();
    }
}