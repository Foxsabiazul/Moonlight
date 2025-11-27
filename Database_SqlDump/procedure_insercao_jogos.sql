-- PROCEDURE para inserção de jogos (parametro para inserir quantos jogos serão gerados. INT )

CREATE OR REPLACE PROCEDURE `sp_inserir_jogos_massa`(IN num_jogos INT)
BEGIN
    DECLARE i INT DEFAULT 1; -- I será o numero de vezes que se repetira o while.
    DECLARE categoria_id_aleatoria BIGINT;
    WHILE i <= num_jogos DO

        --pega um id aleatorio de categoria.
    	SELECT id_categoria INTO categoria_id_aleatoria
        FROM `Categorias`
        ORDER BY RAND()
        LIMIT 1;
       
	    IF categoria_id_aleatoria IS NOT NULL THEN
	        INSERT INTO `Jogos` (
	            `id_categoria`,
	            `titulo`,
	            `descricao`,
	            `preco`,
	            `data_lancamento`,
	            `ativo`
	        )
	        VALUES (
	            categoria_id_aleatoria,
	            CONCAT('Game Teste ', i), -- GAME TESTE 1, GAME TESTE 2 e assim vai..
	            CONCAT('Descrição para o jogo de teste ', i, '. Um título de...'), -- o mesmo aqui.
	            ROUND(RAND() * 50 + 10, 2), -- Preço aleatório entre 10 e 60
	            DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 365 * 5) DAY), -- Data aleatória nos últimos 5 anos
	            'N' -- ativo não.
	        );
	    end if;
	   
	    SET i = i + 1; -- i++ vai iterando até acabar.
   
    END WHILE;
END;

-- Exemplo de uso:
-- CALL sp_inserir_jogos_massa(100);
