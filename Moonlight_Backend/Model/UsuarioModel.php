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

        public function editar($id) {
            $sql = "select * from usuários where id_user = :id_user limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id_user", $id);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_OBJ);
        }

        public function listar() {
            $sql = "select * from usuários order by nm_user";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }

        public function salvar() {
            
            if (!empty($_POST["senha"])) $_POST["senha"] = password_hash($_POST["senha"], PASSWORD_DEFAULT);

            if (empty($_POST["id"])) {
                $sql = "insert into usuários (nm_user, email, senha, data_criacao) 
                values (:nm_user, :email, :senha, :data_criacao)";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":nm_user", $_POST["nome"]);
                $consulta->bindParam(":email", $_POST["email"]);
                $consulta->bindParam(":senha", $_POST["senha"]);
                $dataHoraSQL = (new \DateTime())->format('Y-m-d H:i:s');
                $consulta->bindParam(":data_criacao", $dataHoraSQL);

            } else if (empty($_POST["senha"])) {
                $sql = "update usuários set nm_user = :nm_user, email = :email, where id_user = :id_user limit 1";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":nm_user", $_POST["nome"]);
                $consulta->bindParam(":email", $_POST["email"]);
                $consulta->bindParam(":id_user", $_POST["id"]);

            } else {
                $sql = "update usuários set senha = :senha, nm_user = :nm_user, email = :email where id_user = :id_user limit 1";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(":senha", $_POST["senha"]);
                $consulta->bindParam(":nm_user", $_POST["nome"]);
                $consulta->bindParam(":email", $_POST["email"]);
                $consulta->bindParam(":id_user", $_POST["id"]);
            }

            return $consulta->execute();
        }

        public function excluir($id) {
            $sql = "DELETE FROM usuários WHERE id_user = :id_user limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id_user", $id);

            return $consulta->execute();
        }

        public function getLogin($email) {

            $sql = "select id_user, nm_user, email, senha, data_criacao from usuários where email = :email limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":email", $email);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_OBJ);
        }

    }

?>