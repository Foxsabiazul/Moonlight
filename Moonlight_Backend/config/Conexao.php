<?php
namespace Moonlight_Backend\config;
use PDO;
use PDOException;

class Conexao {
    private static $host = "3306";
    private static $usuario = "root";
    private static $senha = "";
    private static $db = "moonlight_e-commerce";

    // propriedade estatica para armazenar a instancia PDO
    private static ?PDO $instancia = null;

    public static function connect(): PDO {
        // Verifica se a instância PDO já existe
        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
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

        return self::$instancia;
    }

}