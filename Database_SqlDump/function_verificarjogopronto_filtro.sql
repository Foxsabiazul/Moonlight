-- verifica se o jogo está ativo.
-- util para realizar operações de filtragem

-- parametro é o id de algum jogo.

DELIMITER $$
CREATE FUNCTION `fn_verificar_jogo_pronto_venda`(
    id_games_param BIGINT
)
RETURNS BOOLEAN
READS SQL DATA
BEGIN
    DECLARE is_ready INT;

    -- Verifica se o jogo está ativo ('S') e se a categoria referenciada ainda existe (INNER JOIN)

    SELECT COUNT(J.id_games) INTO is_ready
    FROM `Jogos` AS J
    INNER JOIN `Categorias` AS C ON J.id_categoria = C.id_categoria
    WHERE J.id_games = id_games_param
      AND J.ativo = 'S'; -- 'S' = Sim (Ativo)
    RETURN is_ready > 0;
END;

$$

DELIMITER ;

-- exemplo:
-- vai listar apenas os jogos que estão prontos para venda:

SELECT 
    id_games, 
    titulo, 
    preco, 
    fn_verificar_jogo_pronto_venda(id_games) AS pronto_para_venda
FROM 
    Jogos
WHERE 
    fn_verificar_jogo_pronto_venda(id_games) = TRUE; -- ou simplesmente = 1