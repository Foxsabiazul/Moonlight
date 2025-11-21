CREATE DATABASE moonlight_e_commerce;

USE moonlight_e_commerce;

-- -----------------------------------------------------------------------------
-- TABELA USUÁRIOS
-- -----------------------------------------------------------------------------
CREATE TABLE `Usuários`(
    `id_user` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nm_user` VARCHAR(255) NOT NULL COMMENT 'Nome do usuário',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Único (login)',
    `senha` VARCHAR(255) NOT NULL COMMENT 'Hash da senha',
    `data_criacao` DATETIME NOT NULL COMMENT 'Quando foi criado',
    `tipo` ENUM('cliente','admin') NOT NULL DEFAULT 'cliente'
);

-- -----------------------------------------------------------------------------
-- TABELA CATEGORIAS
-- -----------------------------------------------------------------------------
CREATE TABLE `Categorias`(
    `id_categoria` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nm_cat` VARCHAR(255) NOT NULL,
    `desc_cat` VARCHAR(255) NOT NULL
);

-- -----------------------------------------------------------------------------
-- TABELA JOGOS
-- Regra: Excluir Categoria DELETA os Jogos associados. (CASCADE)
-- -----------------------------------------------------------------------------
CREATE TABLE `Jogos`(
    `id_games` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_categoria` BIGINT NOT NULL COMMENT 'Id de categoria',
    `titulo` VARCHAR(255) NOT NULL COMMENT 'Nome do jogo',
    `descricao` VARCHAR(255) COMMENT 'Texto descritivo',
    `preco` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do jogo',
    `imagem` VARCHAR(255) COMMENT 'Caminho/URL da capa',
    `link` VARCHAR(255) COMMENT 'Caminho/URL do arquivo (ou null)',
    `data_lancamento` DATE NOT NULL COMMENT 'Data de lançamento',
    `ativo` ENUM('S', 'N') NOT null,
    
    -- Chave Estrangeira (FK) com ON DELETE CASCADE
    CONSTRAINT `fk_jogos_categoria` FOREIGN KEY (`id_categoria`) 
        REFERENCES `Categorias`(`id_categoria`) ON DELETE CASCADE
);

-- índices na tabela jogos
CREATE INDEX `idx_jogos_titulo` ON `Jogos` (`titulo`);
CREATE INDEX `idx_jogos_preco` ON `Jogos` (`preco`);

-- -----------------------------------------------------------------------------
-- TABELA PEDIDOS
-- Regra: Excluir Usuário DELETA seus Pedidos. (CASCADE)
-- -----------------------------------------------------------------------------
CREATE TABLE `Pedidos`(
    `id_pedido` BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    `id_user` BIGINT NOT NULL COMMENT 'Quem comprou',
    `data_pedido` TIMESTAMP NOT NULL COMMENT 'Data da compra',
    `total` DECIMAL(10, 2) NOT NULL COMMENT 'Valor total para o usuário',
    
    -- Chave Estrangeira (FK) com ON DELETE CASCADE
    CONSTRAINT `fk_pedidos_user` FOREIGN KEY (`id_user`) 
        REFERENCES `Usuários`(`id_user`) ON DELETE CASCADE
);

-- -----------------------------------------------------------------------------
-- TABELA ITENSCOMPRA
-- Regras:
-- 1. Excluir Pedido DELETA seus itens. (CASCADE)
-- 2. Excluir Jogo é RESTRITO se ele já foi vendido. (RESTRICT)
-- -----------------------------------------------------------------------------
CREATE TABLE `ItensCompra`(
    `id_pedido` BIGINT NOT NULL COMMENT 'Qual compra',
    `id_games` BIGINT NOT NULL COMMENT 'Qual jogo foi comprado',
    `preco` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do jogo na data da compra',
    
    PRIMARY KEY (id_pedido, id_games), 
    
    -- FK Pedido com ON DELETE CASCADE
    CONSTRAINT `fk_itenscompra_pedido` FOREIGN KEY (`id_pedido`) 
        REFERENCES `Pedidos`(`id_pedido`) ON DELETE CASCADE,
    
    -- FK Jogo com ON DELETE RESTRICT (Impede a exclusão do histórico de vendas)
    CONSTRAINT `fk_itenscompra_jogo` FOREIGN KEY (`id_games`) 
        REFERENCES `Jogos`(`id_games`) ON DELETE RESTRICT
);

-- índices na tabela ItemCompra
CREATE INDEX `idx_itemcompra_pedido` ON `ItensCompra` (`id_pedido`);
CREATE INDEX `idx_itemcompra_games` ON `ItensCompra` (`id_games`);

-- -----------------------------------------------------------------------------
-- TABELA BIBLIOTECA
-- Regra: Excluir Usuário ou Jogo DELETA o registro na Biblioteca. (CASCADE)
-- -----------------------------------------------------------------------------
CREATE TABLE `Biblioteca`(
    `id_user` BIGINT NOT NULL COMMENT 'Dono do jogo',
    `id_games` BIGINT NOT NULL COMMENT 'Jogo adquirido',
    `data_adicao` TIMESTAMP NOT NULL COMMENT 'Quando foi liberado na conta ou data de acesso',
    
    PRIMARY KEY (id_user, id_games),
    
    -- FK Usuário com ON DELETE CASCADE
    CONSTRAINT `fk_biblioteca_user` FOREIGN KEY (`id_user`) 
        REFERENCES `Usuários`(`id_user`) ON DELETE CASCADE,
        
    -- FK Jogo com ON DELETE CASCADE
    CONSTRAINT `fk_biblioteca_jogo` FOREIGN KEY (`id_games`) 
        REFERENCES `Jogos`(`id_games`) ON DELETE CASCADE
);

-- -----------------------------------------------------------------------------
-- TABELA FAVORITOS
-- Regra: Excluir Usuário ou Jogo DELETA o registro de Favorito. (CASCADE)
-- -----------------------------------------------------------------------------
CREATE TABLE `Favoritos`(
    `id_user` BIGINT NOT NULL,
    `id_games` BIGINT NOT NULL,
    `fav_star` ENUM('1','2','3','4','5', 'indefinido') NOT NULL DEFAULT 'indefinido',
    
    PRIMARY KEY (id_user, id_games),
    
    -- FK Usuário com ON DELETE CASCADE
    CONSTRAINT `fk_favoritos_user` FOREIGN KEY (`id_user`) 
        REFERENCES `Usuários`(`id_user`) ON DELETE CASCADE,
        
    -- FK Jogo com ON DELETE CASCADE
    CONSTRAINT `fk_favoritos_jogo` FOREIGN KEY (`id_games`) 
        REFERENCES `Jogos`(`id_games`) ON DELETE CASCADE
);

-- -----------------------------------------------------------------------------
-- TABELA AUDITORIAPRECO
-- Regra: Excluir Jogo DELETA seu histórico de preços. (CASCADE)
-- -----------------------------------------------------------------------------
CREATE TABLE `AuditoriaPreco`(
    `id_audipreco` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `id_games` BIGINT NOT NULL,
    `preco_antigo` DECIMAL(10, 2) NOT NULL,
    `preco_novo` DECIMAL(10, 2) NOT NULL,
    `data_alteracao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `usuario_alteracao` varchar(255) comment 'Usuario que alterou',
    
    -- FK Jogo com ON DELETE CASCADE
    constraint `fk_auditoria_games` foreign key (`id_games`)
    	references `jogos` (`id_games`) ON DELETE CASCADE
);