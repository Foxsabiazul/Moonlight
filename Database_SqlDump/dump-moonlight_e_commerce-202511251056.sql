-- -----------------------------------------
-- Create database and use
-- -----------------------------------------
CREATE DATABASE IF NOT EXISTS moonlight_e_commerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE moonlight_e_commerce;

-- -----------------------------------------
-- Table: Usuários
-- -----------------------------------------
CREATE TABLE `Usuários` (
    `id_user` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nm_user` VARCHAR(255) NOT NULL COMMENT 'Nome do usuário',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Único (login)',
    `senha` VARCHAR(255) NOT NULL COMMENT 'Hash da senha',
    `data_criacao` DATETIME NOT NULL COMMENT 'Quando foi criado',
    `tipo` ENUM('cliente','admin') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------
-- Table: Categorias
-- -----------------------------------------
CREATE TABLE `Categorias` (
    `id_categoria` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nm_cat` VARCHAR(255) NOT NULL,
    `desc_cat` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------
-- Table: Jogos
-- -----------------------------------------
CREATE TABLE `Jogos` (
    `id_games` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_categoria` BIGINT NOT NULL,
    `titulo` VARCHAR(255) NOT NULL COMMENT 'Nome do jogo',
    `descricao` VARCHAR(255) COMMENT 'Texto descritivo',
    `preco` DECIMAL(10,2) NOT NULL COMMENT 'Preço do jogo',
    `imagem` VARCHAR(255) COMMENT 'Caminho/URL da capa',
    `link` VARCHAR(255) COMMENT 'Caminho/URL do arquivo (ou null)',
    `data_lancamento` DATE NOT NULL COMMENT 'Data de lançamento',
    `ativo` ENUM('S', 'N') NOT NULL,
    CONSTRAINT `fk_jogos_categoria` FOREIGN KEY (`id_categoria`)
        REFERENCES `Categorias`(`id_categoria`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX `idx_jogos_titulo` ON `Jogos` (`titulo`);
CREATE INDEX `idx_jogos_preco` ON `Jogos` (`preco`);

-- -----------------------------------------
-- Table: Pedidos
-- -----------------------------------------
CREATE TABLE `Pedidos` (
    `id_pedido` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_user` BIGINT NOT NULL,
    `data_pedido` DATETIME NOT NULL COMMENT 'Data da compra',
    `total` DECIMAL(10,2) NOT NULL COMMENT 'Valor total para o usuário',
    `preference_id` VARCHAR(50) NOT NULL COMMENT 'id do mercado pago',
    `external_reference` VARCHAR(50) NOT NULL COMMENT 'id pro mercado pago encontrar e notificar',
    `status` ENUM('pendente', 'aprovado', 'reembolsado', 'cancelado') NOT NULL DEFAULT 'pendente',
    CONSTRAINT `fk_pedidos_user` FOREIGN KEY (`id_user`)
        REFERENCES `Usuários`(`id_user`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------
-- Table: ItensCompra
-- -----------------------------------------
CREATE TABLE `ItensCompra` (
    `id_pedido` BIGINT NOT NULL,
    `id_games` BIGINT NOT NULL,
    `preco` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`id_pedido`, `id_games`),
    CONSTRAINT `fk_itenscompra_pedido` FOREIGN KEY (`id_pedido`)
        REFERENCES `Pedidos`(`id_pedido`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_itenscompra_jogo` FOREIGN KEY (`id_games`)
        REFERENCES `Jogos`(`id_games`)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX `idx_itemcompra_pedido` ON `ItensCompra` (`id_pedido`);
CREATE INDEX `idx_itemcompra_games` ON `ItensCompra` (`id_games`);

-- -----------------------------------------
-- Table: Biblioteca
-- -----------------------------------------
CREATE TABLE `Biblioteca` (
    `id_user` BIGINT NOT NULL,
    `id_games` BIGINT NOT NULL,
    `data_adicao` DATETIME NOT NULL,
    PRIMARY KEY (`id_user`, `id_games`),
    CONSTRAINT `fk_biblioteca_user` FOREIGN KEY (`id_user`)
        REFERENCES `Usuários`(`id_user`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_biblioteca_jogo` FOREIGN KEY (`id_games`)
        REFERENCES `Jogos`(`id_games`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------
-- Table: Favoritos
-- -----------------------------------------
CREATE TABLE `Favoritos` (
    `id_user` BIGINT NOT NULL,
    `id_games` BIGINT NOT NULL,
    `fav_star` ENUM('1','2','3','4','5','indefinido') NOT NULL DEFAULT 'indefinido',
    PRIMARY KEY (`id_user`, `id_games`),
    CONSTRAINT `fk_favoritos_user` FOREIGN KEY (`id_user`)
        REFERENCES `Usuários`(`id_user`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_favoritos_jogo` FOREIGN KEY (`id_games`)
        REFERENCES `Jogos`(`id_games`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------
-- Table: AuditoriaPreco
-- -----------------------------------------
CREATE TABLE `AuditoriaPreco` (
    `id_audipreco` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `id_games` BIGINT NOT NULL,
    `preco_antigo` DECIMAL(10,2) NOT NULL,
    `preco_novo` DECIMAL(10,2) NOT NULL,
    `data_alteracao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `usuario_alteracao` VARCHAR(255),
    CONSTRAINT `fk_auditoria_games` FOREIGN KEY (`id_games`)
        REFERENCES `Jogos`(`id_games`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------
-- Trigger AuditoriaPreco
-- -----------------------------------------
DELIMITER $$
CREATE TRIGGER `trg_auditar_preco_jogo`
AFTER UPDATE ON `Jogos`
FOR EACH ROW
BEGIN
    IF OLD.preco <> NEW.preco THEN
        INSERT INTO `AuditoriaPreco` (
            `id_games`,
            `preco_antigo`,
            `preco_novo`,
            `usuario_alteracao`
        )
        VALUES (
            NEW.id_games,
            OLD.preco,
            NEW.preco,
            COALESCE(@usuario_logado, USER())
        );
    END IF;
END;
$$
DELIMITER ;

-- -----------------------------------------
-- Procedure - Inserir Jogos Massa
-- -----------------------------------------
DELIMITER $$
CREATE OR REPLACE PROCEDURE `sp_inserir_jogos_massa`(IN num_jogos INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE categoria_id_aleatoria BIGINT;
    WHILE i <= num_jogos DO
    
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
	            CONCAT('Game Teste ', i),
	            CONCAT('Descrição para o jogo de teste ', i, '. Um título de ação e aventura.'),
	            ROUND(RAND() * 50 + 10, 2),
	            DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 365 * 5) DAY),
	            'N'
	        );
	    end if;
	   
	    SET i = i + 1;
   
    END WHILE;
END;
$$
DELIMITER ;

-- -----------------------------------------
-- Função fn_verificar_jogo_comprado
-- -----------------------------------------
DELIMITER $$
CREATE FUNCTION `fn_verificar_jogo_comprado`(
    id_user_param BIGINT,
    id_games_param BIGINT
)
RETURNS BOOLEAN
READS SQL DATA
BEGIN
    DECLARE possui INT;
    SELECT COUNT(*) INTO possui
    FROM `Biblioteca` AS B
    WHERE B.id_user = id_user_param AND B.id_games = id_games_param;
    RETURN possui > 0;
END;
$$
DELIMITER ;

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

-- -----------------------------------------
-- Categorias pré-cadastradas
-- -----------------------------------------
INSERT INTO `Categorias` (`id_categoria`, `nm_cat`, `desc_cat`) VALUES
(1, 'Ação', 'Gênero que enfatiza desafios físicos, como coordenação olho-mão e tempo de reação.'),
(2, 'Corrida', 'Jogos que simulam corridas de veículos motorizados ou outros meios.'),
(3, 'Estratégia', 'Jogos que exigem planejamento, táticas e manobras para alcançar a vitória.'),
(4, 'FPS', '(First-Person Shooter, ou Jogo de Tiro em Primeira Pessoa) é um gênero de jogo eletrônico focado em combate com armas sob a perspectiva do protagonista.'),
(5, 'Plataforma', 'Jogo de Plataforma, é um gênero de jogo eletrônico ambientado em cenários desafiadores, com pulos entre plataformas para poder passar de fases.'),
(6, 'Puzzle', 'Jogos de Puzzle (Quebra-Cabeça) são um gênero que desafia o jogador a usar a lógica, a capacidade de observação, e a habilidade de resolver problemas para avançar.'),
(7, 'RPG', 'Role-Playing Game, onde o jogador assume o papel de um personagem em um mundo fictício, desenvolvendo habilidades e completando missões.');

-- -----------------------------------------
-- Jogos pré-cadastrados
-- -----------------------------------------
INSERT INTO `Jogos` (`id_categoria`, `titulo`, `descricao`, `preco`, `imagem`, `link`, `data_lancamento`, `ativo`) VALUES
(4, 'Cyberpunk 2077', 'RPG de ação e aventura em mundo aberto ambientado em Night City, uma megalópole obcecada por poder, glamour e modificações corporais.', 199.99, '1763675630.jpg', 'https://store.steampowered.com/app/1091500/', '2020-12-10', 'S'),
(4, 'Doom Eternal', 'Retorno do Slayer com uma sede de vingança insaciável contra as forças do Inferno.', 149.99, '1763675061.jpg', 'https://store.steampowered.com/app/782330/', '2020-03-20', 'S'),
(6, 'The Witness', 'Um jogo de exploração em primeira pessoa em uma ilha misteriosa, resolvendo mais de 500 quebra-cabeças.', 74.90, '1763677830.jpg', 'https://www.epicgames.com/store/pt-BR/p/the-witness', '2016-01-26', 'S'),
(6, 'Portal 2', 'Quebra-cabeças cooperativos e individuais baseados em física.', 32.99, '1763676691.jpg', 'https://store.steampowered.com/app/620/', '2011-04-18', 'S'),
(5, 'Hollow Knight', 'Aventura de ação 2D com estilo Metroidvania, ambientada em um vasto mundo de insetos e heróis arruinados.', 46.99, '1763675831.jpg', 'https://www.gog.com/game/hollow_knight', '2017-02-24', 'S'),
(5, 'Cuphead', 'Cuphead é um jogo clássico de plataformas e tiros em 2D, intensamente focado nas batalhas contra chefões. Inspirado por desenhos dos anos 30, o visual e o áudio foram detalhadamente recriados com as mesmas técnicas daquela era: animação desenhada à mão', 73.90, '1763686313.jpg', 'https://store.steampowered.com/app/268910/Cuphead/', '2017-09-29', 'S'),
(7, 'The Elder Scrolls V: Skyrim', 'RPG épico em mundo aberto com dragões e magia.', 99.99, '1763677337.jpg', 'https://steam-copy.com/skyrim', '2011-11-11', 'S'),
(7, 'The Witcher 3: Wild Hunt', 'Diante de você, há um continente infestado de monstros devastado pela guerra que pode ser explorado à vontade. Seu contrato atual? Rastrear o Filho da Profecia, uma arma viva que pode alterar a forma do mundo. Veleje pelos mares abertos, mergulhe em busca', 129.99, '1763688910.jpg', 'https://store.steampowered.com/app/292030/The_Witcher_3_Wild_Hunt/', '2015-05-18', 'S'),
(3, 'Age of Empires II: Definitive Edition', 'Estratégia em tempo real com 1000 anos de história.', 39.99, '1763675317.jpg', 'https://www.xbox.com/pt-BR/games/store/age-of-empires-ii-definitive-edition', '2019-11-14', 'S'),
(3, "Sid Meier's Civilization VI", 'Civilization VI traz novas formas de interagir com seu mundo: as cidades agora se expandem fisicamente pelo mapa, a pesquisa ativa de tecnologia e cultura revela um novo potencial e os líderes rivais seguirão seus próprios planos com base em suas caracter', 129.00, '1763689491.jpg', 'https://store.steampowered.com/app/289070/Sid_Meiers_Civilization_VI/', '2016-10-21', 'S'),
(1, 'Red Dead Redemption 2', 'Aventura épica de mundo aberto no Velho Oeste, com foco em narrativa, exploração e tiroteios intensos.', 199.99, '1763697547.jpg', 'https://store.epicgames.com/pt-BR/p/red-dead-redemption-2', '2018-10-26', 'S'),
(1, 'God of War (2018)', 'RPG de ação com combate visceral e profundo, focado na jornada de Kratos e seu filho Atreus na mitologia nórdica.', 99.99, '1763696834.jpg', 'https://store.steampowered.com/app/1593500/God_of_War/', '2018-04-20', 'S'),
(2, 'Forza Horizon 5', 'Jogo de corrida em mundo aberto que se passa no México, com centenas de carros e um grande foco em festivais e exploração.', 149.99, '1763698065.jpg', 'https://store.steampowered.com/app/1551360/Forza_Horizon_5/', '2021-11-09', 'S'),
(2, 'Need for Speed Unbound', 'Corrida de rua arcade com estilo de arte único, perseguições policiais de alto risco e customização detalhada de carros.', 129.99, '1763699234.jpg', 'https://store.steampowered.com/app/1846380/Need_for_Speed_Unbound/', '2022-12-02', 'S');

-- -----------------------------------------
-- Usuário “Carlinhos” Admin
-- -----------------------------------------
INSERT INTO `Usuários` (`nm_user`, `email`, `senha`, `data_criacao`, `tipo`) VALUES
('Carlinhos', 'carlos@gmail.com', '$2y$10$eFtZ8CypSv3guw.ehsyNXuzSnQPOtMjQdDKxghKc/21GjMJ/z8xb6', '2025-10-30 17:54:59', 'admin');
