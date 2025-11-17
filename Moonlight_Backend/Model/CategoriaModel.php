<?php

namespace Moonlight_Backend\Model;
use PDO;
/**
 * namespace serve pra definir caminhos para autoload de classes e para nao se confundir com metódos publicos do php. (o Composer precisa disso!)
 */

class CategoriaModel {
    //declaração de variáveis 
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
    public function inserirCategoria(array $dados): bool {
        $sql = "INSERT INTO categorias (nm_cat, desc_cat) 
                VALUES (:nm_cat, :desc_cat)";
        
        $consulta = $this->pdo->prepare($sql);
        
        $consulta->bindParam(":nm_cat", $dados['nm_cat']);
        $consulta->bindParam(":desc_cat", $dados['desc_cat']);

        return $consulta->execute();
    }

    /**
     * @param array $dados Os dados do usuário. Pode conter a chave 'senha' ou não.
     * @return bool
     * 
     * Faz update na tabela Categorias, com base nos dados fornecidos da controller
     */
    public function atualizarCategoria(array $dados): bool {
        
        $setClauses = "nm_cat = :nm_cat, desc_cat = :desc_cat";
        $parameters = [
            ":nm_cat" => $dados['nm_cat'],
            ":desc_cat"   => $dados['desc_cat'],
            ":id_categoria" => $dados['id_categoria']
        ];
        
        $sql = "UPDATE categorias SET {$setClauses} WHERE id_categoria = :id_categoria LIMIT 1";
        $consulta = $this->pdo->prepare($sql);
        
        // Faz o bind de todos os parâmetros dinamicamente
        foreach ($parameters as $key => $value) {
            // $key: armazena a chave do array (ex: ":nm_cat", ":desc_cat")
            // $value: armazena o valor do array (ex: "FPS", "lorem ipson")
            // $parameters[$key]: armazena a referencia do valor dessa chave no array, ou seja, é a mesma coisa que puxar o valor dele.
            $consulta->bindParam($key, $parameters[$key]);
        }

        return $consulta->execute();
    }

    public function listarCategoria() {
        $sql = "select * from categorias order by nm_cat";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    // editar não é update, ele serve para resgatar os valores do banco para trazer à interface do formulario de Categoria
    public function editarCategoria($id) {
        $sql = "select * from categorias where id_categoria = :id_categoria limit 1";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":id_categoria", $id);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_OBJ);
    }

    public function excluirCategoria($id): bool {
        $sql = "DELETE FROM categorias WHERE id_categoria = :id_categoria limit 1";
        $consulta = $this->pdo->prepare($sql);
        $consulta->bindParam(":id_categoria", $id);

        return $consulta->execute();
    }

}


