<?php

class Conexao {
    private static $host = "localhost:3306";
    private static $usuario = "root";
    private static $senha = "";
    private static $db = "moonlight_e-commerce";

    public static function connect(){
        try {
            return new PDO("mysql:host=".self::$host.";dbname="
            .self::$db.";charset=utf8",self::$usuario, self::$senha);
        }
        catch (PDOException $e) {
            die("Erro ao conectar: {$e->getMessage()}");
        }
    }
}