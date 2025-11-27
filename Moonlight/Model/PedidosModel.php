<?php

    namespace Moonlight\Model;

use Exception;
use Moonlight\Config\Logger;
use Moonlight\Config\ModalMessage;
use PDO;
use PDOException;
use RuntimeException;

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

        $sqlBusca = "SELECT id_user, id_pedido, status from pedidos where external_reference = :external_reference";
        $consultaBusca = $this->pdo->prepare($sqlBusca);
        $consultaBusca->bindParam(":external_reference", $external_reference);
        $consultaBusca->execute();
        $pedido = $consultaBusca->fetch(PDO::FETCH_OBJ);

        if(!$pedido){
            $errorText = "Falhou no select de alguma forma em atualizarStatusPedidoPorExternalReference() no PedidosModel.";
            Logger::logError(new \RuntimeException($errorText), "SELECT_DB_ERROR");
        }

        $deveAdicionarNaBiblioteca = ($status === "aprovado" && $pedido->status !== "aprovado");

        $sql = "UPDATE pedidos SET status = :status WHERE external_reference = :external_reference";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":status", $status);
        $consulta->bindParam(":external_reference", $external_reference);

        $sucesso = $consulta->execute();

        if($sucesso && $deveAdicionarNaBiblioteca){
            $this->adicionarJogosDoPedidoNaBiblioteca($pedido->id_pedido, $pedido->id_user);
        }

        return $sucesso;
    }

    private function adicionarJogosDoPedidoNaBiblioteca(int $id_pedido, int $id_user){
        $sql = "SELECT id_games from itenscompra where id_pedido = :id_pedido";
        $consultaJogos = $this->pdo->prepare($sql);
        $consultaJogos->bindParam(":id_pedido", $id_pedido, PDO::PARAM_INT);
        $consultaJogos->execute();
        $jogos = $consultaJogos->fetchAll(PDO::FETCH_OBJ);

        $sqlBiblioteca = "INSERT IGNORE INTO biblioteca (id_user, id_games, data_adicao) VALUES (:id_user, :id_games, NOW())";

        $consultaBiblioteca = $this->pdo->prepare($sqlBiblioteca);
        $consultaBiblioteca->bindParam(":id_user", $id_user, PDO::PARAM_INT);

        foreach($jogos as $jogo){
            $consultaBiblioteca->bindParam(":id_games", $jogo->id_games, PDO::PARAM_INT);
            $consultaBiblioteca->execute();
        }

    }
}