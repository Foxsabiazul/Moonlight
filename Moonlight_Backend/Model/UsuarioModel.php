<?php
    namespace Moonlight_Backend\Model;
    use PDO;

    /**
     * namespace serve pra definir caminhos para autoload de classes e para nao se confundir com metódos publicos do php. (o Composer precisa disso!)
     */

    class UsuarioModel {
        private PDO $pdo;

        public function __construct(
            PDO $pdo,
        )
        {
            $this->pdo = $pdo;
        }

        public function getUsuario($email){
            $sql = "select * from usuários where email = :email limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":email", $email);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_OBJ);
        }

    }

?>