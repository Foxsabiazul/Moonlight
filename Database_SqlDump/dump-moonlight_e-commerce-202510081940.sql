CREATE DATABASE moonlight_e_commerce;

USE moonlight_e_commerce;


CREATE TABLE `Usuários`(
    `id_user` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nm_user` VARCHAR(255) NOT NULL COMMENT 'Nome do usuário',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Único (login)',
    `senha` VARCHAR(255) NOT NULL COMMENT 'Hash da senha',
    `data_criacao` DATETIME NOT NULL COMMENT 'Quando foi criado'
    `tipo` ENUM('cliente','admin') NOT NULL DEFAULT 'cliente'
);


CREATE TABLE `Categorias`(
    `id_categoria` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nm_cat` VARCHAR(255) NOT NULL,
    `desc_cat` VARCHAR(255) NOT NULL
);

---


CREATE TABLE `Jogos`(
    `id_games` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_categoria` BIGINT NOT NULL COMMENT 'Id de categoria',
    `titulo` VARCHAR(255) NOT NULL COMMENT 'Nome do jogo',
    `descricao` VARCHAR(255) COMMENT 'Texto descritivo',
    `preco` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do jogo',
    `imagem` VARCHAR(255) COMMENT 'Caminho/URL da capa',
    `link` VARCHAR(255) COMMENT 'Caminho/URL do arquivo (ou null)',
    `data_lancamento` DATE NOT NULL COMMENT 'Data de lançamento',
    `ativo` ENUM('S', 'N') NOT NULL
    
    -- Chave Estrangeira (FK)
    CONSTRAINT `fk_jogos_categoria` FOREIGN KEY (`id_categoria`) 
        REFERENCES `Categorias`(`id_categoria`)
);

-- índice na tabela jogos para otimizar buscas por titulos e preços de jogo
CREATE INDEX `idx_jogos_titulo` ON `Jogos` (`titulo`);
CREATE INDEX `idx_jogos_preco` ON `Jogos` (`preco`);

---


CREATE TABLE `Pedidos`(
    -- Corrigido para AUTO_INCREMENT, que é comum para tabelas principais
    `id_pedido` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    `id_user` BIGINT NOT NULL COMMENT 'Quem comprou',
    `data_pedido` TIMESTAMP NOT NULL COMMENT 'Data da compra',
    `total` DECIMAL(10, 2) NOT NULL COMMENT 'Valor total para o usuário',
    
    -- Chave Estrangeira (FK)
    CONSTRAINT `fk_pedidos_user` FOREIGN KEY (`id_user`) 
        REFERENCES `Usuários`(`id_user`)
);

---

-- Tabela de relacionamento N:M (Pedido tem M Jogos)
CREATE TABLE `ItensCompra`(
    `id_pedido` BIGINT NOT NULL COMMENT 'Qual compra',
    `id_games` BIGINT NOT NULL COMMENT 'Qual jogo foi comprado',
    `preco` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do jogo na data da compra',
    
    -- Chave Primária Composta (Corrigida a sintaxe)
    PRIMARY KEY (id_pedido, id_games), 
    
    -- Chaves Estrangeiras (FKs)
    CONSTRAINT `fk_itenscompra_pedido` FOREIGN KEY (`id_pedido`) 
        REFERENCES `Pedidos`(`id_pedido`),
    CONSTRAINT `fk_itenscompra_jogo` FOREIGN KEY (`id_games`) 
        REFERENCES `Jogos`(`id_games`)
);

-- índice na tabela ItemCompra para otimizar buscas por pedido e jogo
CREATE INDEX `idx_itemcompra_pedido` ON `ItensCompra` (`id_pedido`);
CREATE INDEX `idx_itemcompra_games` ON `ItensCompra` (`id_games`);

-- Tabela de relacionamento N:M (Usuário tem M Jogos)
CREATE TABLE `Biblioteca`(
    `id_user` BIGINT NOT NULL COMMENT 'Dono do jogo',
    `id_games` BIGINT NOT NULL COMMENT 'Jogo adquirido',
    `data_adicao` TIMESTAMP NOT NULL COMMENT 'Quando foi liberado na conta ou data de acesso',
    
    -- Chave Primária Composta 
    PRIMARY KEY (id_user, id_games),
    
    -- Chaves Estrangeiras (FKs)
    CONSTRAINT `fk_biblioteca_user` FOREIGN KEY (`id_user`) 
        REFERENCES `Usuários`(`id_user`),
    CONSTRAINT `fk_biblioteca_jogo` FOREIGN KEY (`id_games`) 
        REFERENCES `Jogos`(`id_games`)
);


-- Tabela de relacionamento N:M (Usuário tem M Jogos Favoritos)
CREATE TABLE `Favoritos`(
    `id_user` BIGINT NOT NULL,
    `id_games` BIGINT NOT NULL,
    `fav_star` ENUM('1','2','3','4','5', 'indefinido') NOT NULL DEFAULT 'indefinido',
    
    -- Chave Primária Composta
    PRIMARY KEY (id_user, id_games),
    
    -- Chaves Estrangeiras (FKs)
    CONSTRAINT `fk_favoritos_user` FOREIGN KEY (`id_user`) 
        REFERENCES `Usuários`(`id_user`),
    CONSTRAINT `fk_favoritos_jogo` FOREIGN KEY (`id_games`) 
        REFERENCES `Jogos`(`id_games`)
);

CREATE TABLE `AuditoriaPreco`(
    `id_audipreco` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `id_games` BIGINT NOT NULL,
    `preco_antigo` DECIMAL(10, 2) NOT NULL,
    `preco_novo` DECIMAL(10, 2) NOT NULL,
    `data_alteracao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `usuario_alteracao` varchar(255) comment 'Usuario que alterou',
    FOREIGN KEY (`id_games`) REFERENCES `Jogos`(`id_games`)
);

-- TRIGGER para jogos: Verifica se a coluna 'preco' foi alterada e avisa quem foi que mudou.

DELIMITER //

CREATE TRIGGER `trg_auditar_preco_jogo`
AFTER UPDATE ON `Jogos` 
FOR EACH ROW
BEGIN
    -- Verifica se a coluna 'preco' foi alterada
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
END //

DELIMITER ;

insert into usuários (nm_user, email, senha, data_criacao, tipo) values ('Carlinhos', 'carlos@gmail.com', '1234', '2025-10-30 17:54:59', 'admin');

-- RESTO DAS FUNÇÕES ESTÃO EM UM TXT NA AREA DE TRABALHO.