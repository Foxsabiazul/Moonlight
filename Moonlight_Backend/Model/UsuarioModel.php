<?php
    namespace Moonlight_Backend\Model;

use Moonlight_Backend\config\Logger;
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

        /**
         * @param array $dados Os dados do usuário, com a senha já com hash.
         * @return bool
         */
        public function inserirUsuario(array $dados): bool {
            $sql = "INSERT INTO usuários (nm_user, email, senha, data_criacao, tipo) 
                    VALUES (:nm_user, :email, :senha, :data_criacao, :tipo)";
            
            $consulta = $this->pdo->prepare($sql);
            
            $consulta->bindParam(":nm_user", $dados['nm_user']);
            $consulta->bindParam(":email", $dados['email']);
            $consulta->bindParam(":senha", $dados['senha']);
            $consulta->bindParam(":data_criacao", $dados['data_criacao']);
            $consulta->bindParam(":tipo", $dados['tipo']);

            return $consulta->execute();
        }

        /**
         * @param array $dados Os dados do usuário. Pode conter a chave 'senha' ou não.
         * @return bool
         * 
         * Faz update na tabela Usuários, com base nos dados fornecidos d
         */
        public function atualizarUsuario(array $dados): bool {
            
            $setClauses = "nm_user = :nm_user, email = :email, tipo = :tipo";
            $parameters = [
                ":nm_user" => $dados['nm_user'],
                ":email"   => $dados['email'],
                ":tipo" => $dados['tipo'],
                ":id_user" => $dados['id_user']
            ];
            
            // Se a senha foi fornecida no Controller, adiciona a string do comando SQL para a senha dentro do $setClauses.
            if (isset($dados['senha'])) {
                $setClauses .= ", senha = :senha";
                $parameters[":senha"] = $dados['senha'];
            }
            
            $sql = "UPDATE usuários SET {$setClauses} WHERE id_user = :id_user LIMIT 1";
            $consulta = $this->pdo->prepare($sql);
            
            // Faz o bind de todos os parâmetros dinamicamente
            foreach ($parameters as $key => $value) {
                // $key: armazena a chave do array (ex: ":nm_user", ":email", ou ":senha")
                // $value: armazena o valor do array (ex: "João", "joao@x.com", ou o hash da senha)
                // $parameters[$key]: armazena a referencia do valor dessa chave no array, ou seja, é a mesma coisa que puxar o valor dele.
                $consulta->bindParam($key, $parameters[$key]);
            }

            return $consulta->execute();
        }

        public function listarUsuario() {
            $sql = "select * from usuários order by nm_user";
            $consulta = $this->pdo->prepare($sql);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_OBJ);
        }

        // editar não é update, ele serve para resgatar os valores do banco para trazer à interface do formulario de Usuario
        public function editarUsuario($id) {
            $sql = "select * from usuários where id_user = :id_user limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id_user", $id);
            $consulta->execute();

            return $consulta->fetch(PDO::FETCH_OBJ);
        }

        public function excluirUsuario($id) {
            $sql = "DELETE FROM usuários WHERE id_user = :id_user limit 1";
            $consulta = $this->pdo->prepare($sql);
            $consulta->bindParam(":id_user", $id);

            return $consulta->execute();
        }

        /**
         * @param string $email pesquisará os dados com base no email
         * @return object com todos os dados.
         * @throws \PDOException Se houver um erro de banco de dados inesperado.
         */
        public function buscarPorEmail(string $email): ?object {
            try {
                $sql = "SELECT id_user, nm_user, senha, data_criacao, tipo FROM usuários WHERE email = :email";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(':email', $email);
                $consulta->execute();
                $dadosUsuario = $consulta->fetch(PDO::FETCH_OBJ);

                return $dadosUsuario ?: null;

            } catch (\PDOException $e) {
                // Logamos erros graves de SQL e propagamos.
                Logger::logError($e, "LOGIN_DB_ERROR");
                throw $e;
            }
        }

    }

?>