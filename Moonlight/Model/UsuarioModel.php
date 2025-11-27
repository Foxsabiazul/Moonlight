<?php
    namespace Moonlight\Model;

use Exception;
use Moonlight\Config\Logger;
use Moonlight\Config\ModalMessage;
use PDO;
use PDOException;

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
         * @return int O ID do usuário inserido.
         * @throws \Moonlight\Config\ModalMessage Se houver violação de regra de negócio (ex: e-mail duplicado).
         * @throws \PDOException Se houver um erro de banco de dados inesperado.
         * @throws \Exception Para falhas de execução desconhecidas.
         */
        public function inserirUsuario(array $dados): int {
            $sql = "INSERT INTO usuários (nm_user, email, senha, data_criacao, tipo) 
                    VALUES (:nm_user, :email, :senha, :data_criacao, :tipo)";
            
            $consulta = $this->pdo->prepare($sql);
            
            $consulta->bindParam(":nm_user", $dados['nm_user']);
            $consulta->bindParam(":email", $dados['email']);
            $consulta->bindParam(":senha", $dados['senha']);
            $consulta->bindParam(":data_criacao", $dados['data_criacao']);
            $consulta->bindParam(":tipo", $dados['tipo']);

            try {
                if ($consulta->execute()) {
                    // Sucesso: Retorna o ID
                    return (int) $this->pdo->lastInsertId();
                }
                // Se execute falhou sem lançar exceção (raro no PDO), lançamos uma exceção genérica
                throw new \Exception("Falha desconhecida ao executar inserção de usuário.");

            } catch (\PDOException $e) {
                
                // Código de erro 23000 é para violação de integridade (UNIQUE key, NOT NULL, FK)
                if ($e->getCode() == 23000) {
                    
                    if (str_contains($e->getMessage(), 'email')) {
                        // Isso interrompe o fluxo e envia a mensagem amigável para o Controller.
                        // **LANÇA EXCEÇÃO DE REGRA DE NEGÓCIO**
                        throw new ModalMessage(
                            "E-mail já cadastrado.", 
                            "O e-mail {$dados['email']} já está sendo usado. Tente fazer login."
                        );
                        Logger::logError($e, "INSERT_DB_ERROR");
                    }
                    // Se for outra violação de 23000 (ex: NOT NULL não tratado), deixamos a PDOException propagar
                }

                // **LANÇA EXCEÇÃO DE BANCO DE DADOS**
                // Propaga a PDOException para que o Controller a capture e logue.
                throw $e;
            }
        }

        /**
         * @param array $dados Os dados do usuário. Pode conter a chave 'senha' ou não.
         * @return bool
         * 
         * Faz update na tabela Usuários, com base nos dados fornecidos d
         */
        public function atualizarUsuario(array $dados): bool {

            $emailNovo = $dados['email'];
            $idAtual = $dados['id_user']; // ID do usuário logado (Seguro)

            // CHECAGEM DE E-MAIL DUPLICADO
            // selecione o id de usuario onde o email é igual e o id de usuario é diferente do id do usuario atual
            $sqlCheck = "SELECT id_user FROM usuários WHERE email = :email AND id_user != :id_atual LIMIT 1";
            $consultaCheck = $this->pdo->prepare($sqlCheck);
            $consultaCheck->bindParam(':email', $emailNovo);
            $consultaCheck->bindParam(':id_atual', $idAtual);
            $consultaCheck->execute();
            
            if ($consultaCheck->fetchColumn()) {
                // Email já em uso por outro usuário. Lança exceção de regra de negocios.
                throw new ModalMessage(
                    "E-mail já em uso", 
                    "O e-mail {$emailNovo} já pertence a outra conta. Escolha um novo e-mail."
                );
            }
            
            $setClauses = "nm_user = :nm_user, email = :email";
            $parameters = [
                ":nm_user" => $dados['nm_user'],
                ":email"   => $dados['email'],
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

            try{
                return $consulta->execute();
            } catch(\PDOException $e){
                Logger::logError($e, "UPDATE_DB_ERROR");
                throw $e;
            }
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
        public function buscarPorEmail(string $email): object {
            try {
                $sql = "SELECT id_user, nm_user, senha, data_criacao, tipo FROM usuários WHERE email = :email";
                $consulta = $this->pdo->prepare($sql);
                $consulta->bindParam(':email', $email);
                $consulta->execute();
                $dadosUsuario = $consulta->fetch(PDO::FETCH_OBJ);

                return $dadosUsuario;

            } catch (\PDOException $e) {
                // Logamos erros graves de SQL e propagamos.
                Logger::logError($e, "LOGIN_DB_ERROR");
                throw $e;
            }
        }

    }

?>