
-- PROCEDURE para inserção de jogos (parametro para inserir quantos jogos serão gerados. INT )

CREATE PROCEDURE `sp_inserir_jogos_massa`(IN num_jogos INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    -- Variável para armazenar um ID de categoria (assumimos que o ID 1 existe)
    DECLARE categoria_id_aleatoria BIGINT DEFAULT 1; 

    WHILE i <= num_jogos DO
        
        -- Se você tiver múltiplas categorias, use esta linha para selecionar um ID aleatório
        -- SELECT id_categoria INTO categoria_id_aleatoria FROM Categorias ORDER BY RAND() LIMIT 1;
        
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
            CONCAT('Game Teste ', i),
            CONCAT('Descrição para o jogo de teste ', i, '. Um título de ação e aventura.'),
            ROUND(RAND() * 50 + 10, 2), -- Preço aleatório entre 10 e 60
            DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 365 * 5) DAY), -- Data aleatória nos últimos 5 anos
            'N'
        );
        
        SET i = i + 1;
    END WHILE;
END;

-- Exemplo de uso:
-- CALL sp_inserir_jogos_massa(100);


-- função para verificar rapidamente se o usuario já possui um determinado jogo antes de tentar comprá-lo ou visualizá-lo. (VAI RETORNAR BOOLEAN)
-- parametros = id_user e id_games

CREATE FUNCTION `fn_verificar_jogo_comprado`(
    id_user_param BIGINT, 
    id_games_param BIGINT
)
RETURNS BOOLEAN
READS SQL DATA
BEGIN
    DECLARE possui INT;

    -- Conta quantos registros existem na Biblioteca com o ID do Usuário e o ID do Jogo
    -- Tabela: `Biblioteca` | Colunas: `id_user` e `id_games` (Seus nomes)
    SELECT COUNT(*)
    INTO possui
    FROM `Biblioteca` AS B
    WHERE B.id_user = id_user_param AND B.id_games = id_games_param;

    -- Retorna TRUE (1) se o jogo foi encontrado e FALSE (0) caso contrário
    RETURN possui > 0;
END;


--⚖️ Tabela de Diferenças Chave
--Esta tabela resume as diferenças cruciais entre os dois conceitos, que é um tópico comum em provas:

-- Recurso	                Stored Procedure	                                Function

-- Palavra-Chave	        CREATE PROCEDURE	                                CREATE FUNCTION

-- Chamada	                CALL sp_nome()	                                    Usada em expressões SQL (SELECT fn_nome())

-- Retorno	                Não obrigatório (pode retornar 0 ou N resultados)	Obrigatório (deve retornar 1 valor único/escalar)

-- Manipulação de Dados	    Pode usar SELECT, INSERT, UPDATE, DELETE	        Tipicamente READS SQL DATA (apenas leitura)

-- Uso em SQL	            Não pode ser usada em SELECT, WHERE, HAVING	        Pode ser usada em SELECT, WHERE, HAVING
