<?php

    namespace Moonlight\Model;

use Exception;
use Moonlight\Config\Logger;
use Moonlight\Config\ModalMessage;
use PDO;
use PDOException;

class BibliotecaModel {
    private PDO $pdo;
    
    public function __construct(
        PDO $pdo,
    )
    {
        $this->pdo = $pdo;
    }

    public function listarJogosBiblioteca(int $id_user){
        $sqlBiblioteca = "
        SELECT 
        date_format(b.data_adicao, '%d/%m/%Y %H:%i') dt,
        j.id_games,
        j.titulo,
        j.descricao,
        j.imagem,
        j.link
        FROM biblioteca AS b
        INNER JOIN jogos AS j ON b.id_games = j.id_games
        WHERE b.id_user = :id_user
        ORDER BY j.titulo
        ";
        try{
            $consultaBiblioteca = $this->pdo->prepare($sqlBiblioteca);
            $consultaBiblioteca->bindParam(":id_user", $id_user, PDO::PARAM_INT);
            $consultaBiblioteca->execute();
            return $consultaBiblioteca->fetchAll(PDO::FETCH_OBJ);
        } catch(\PDOException $e){

            Logger::logError($e, "SELECT_DB_ERROR");
            throw $e;
        }
    }

    public function usuarioPossuiJogo(int $id_user, int $id_games){
        try{
            $sqlBiblioteca = "SELECT TRUE FROM biblioteca WHERE id_user = :id_user AND id_games = :id_games LIMIT 1";

            $consultaBiblioteca = $this->pdo->prepare($sqlBiblioteca);
            $consultaBiblioteca->bindParam(":id_user", $id_user, PDO::PARAM_INT);
            $consultaBiblioteca->bindParam(":id_games", $id_games, PDO::PARAM_INT);
            $consultaBiblioteca->execute();

            return (bool)$consultaBiblioteca->fetchColumn();
        } catch(\PDOException $e){
            Logger::logError($e, "SELECT_DB_ERROR_USUARIO_POSSUI_JOGO");
            throw $e;
        }
    }
}