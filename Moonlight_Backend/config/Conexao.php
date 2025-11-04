<?php
namespace Moonlight_Backend\config;
use PDO;
use PDOException;

use App\Model;

class Conexao {
    private static $host = "localhost:3306";
    private static $usuario = "root";
    private static $senha = "";
    private static $db = "moonlight_e_commerce";

    public static function connect(): PDO {
        try {
            return new PDO(
                "mysql:host=".self::$host.
                ";dbname=".self::$db.
                ";charset=utf8",
                self::$usuario,
                self::$senha
            );
        } catch (PDOException $e) {
            // Em caso de erro na conexão, paramos a aplicação.
            die("Erro ao conectar: {$e->getMessage()}");
        }
    }

}