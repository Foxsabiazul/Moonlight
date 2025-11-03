CREATE DATABASE moonlight_e_commerce;

USE moonlight_e_commerce;

-- Tabela Usuários
CREATE TABLE `Usuários`(
    `id_user` BIGINT NOT NULL PRIMARY KEY, -- PK adicionada
    `nm_user` VARCHAR(255) NOT NULL COMMENT 'Nome do usuário',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Único (login)', -- Adicionado UNIQUE
    `senha` VARCHAR(255) NOT NULL COMMENT 'Hash da senha',
    `data_criacao` DATETIME NOT NULL COMMENT 'Quando foi criado',
    `type_user` ENUM('user','admin') NOT NULL DEFAULT 'user'
);

-- Tabela Jogos
CREATE TABLE `Jogos`(
    `id_games` BIGINT NOT NULL PRIMARY KEY, -- PK adicionada
    `titulo` VARCHAR(255) NOT NULL COMMENT 'Nome do jogo',
    `descricao` VARCHAR(255) COMMENT 'Texto descritivo', -- Corrigido o nome da coluna e o NOT NULL é opcional aqui
    `preco` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do jogo',
    `imagem` VARCHAR(255) COMMENT 'Caminho/URL da capa',
    `link` VARCHAR(255) COMMENT 'Caminho/URL do arquivo (ou null)',
    `data_lancamento` DATE NOT NULL COMMENT 'Data de lançamento'
);

-- Tabela Pedido (renomeada de 'Pedido' para 'Pedidos' e 'id_orders' para 'id_pedido' para consistência)
CREATE TABLE `Pedidos`(
    `id_pedido` BIGINT NOT NULL PRIMARY KEY, -- PK adicionada
    `user_id` BIGINT NOT NULL COMMENT 'Quem comprou',
    `data_pedido` TIMESTAMP NOT NULL COMMENT 'Data da compra',
    `total` DECIMAL(10, 2) NOT NULL COMMENT 'Valor total para o usuário com descrição se possivel'
);

-- Tabela ItensCompra
CREATE TABLE `ItensCompra`(
    `id_itens` BIGINT NOT NULL PRIMARY KEY, -- PK adicionada
    `order_id` BIGINT NOT NULL COMMENT 'Qual compra',
    `game_id` BIGINT NOT NULL COMMENT 'Qual jogo foi comprado',
    `preco` DECIMAL(10, 2) NOT NULL COMMENT 'Preço do jogo na data da compra'
);

-- Tabela Biblioteca
CREATE TABLE `Biblioteca`(
    `id_library` BIGINT NOT NULL PRIMARY KEY,
    `user_id` BIGINT NOT NULL COMMENT 'Dono do jogo',
    `games_id` BIGINT NOT NULL COMMENT 'Jogo adquirido',
    `data_adicao` TIMESTAMP NOT NULL COMMENT 'Quando foi liberado na conta ou data de acesso'
    -- A PRIMARY KEY já estava definida. Considere uma chave composta (user_id, games_id) em vez de id_library, dependendo da necessidade.
);

-- Tabela Categorias (Corrigido o tipo de `id_games` para BIGINT)
CREATE TABLE `Categorias`(
    `id_catg` BIGINT NOT NULL PRIMARY KEY,
    -- O campo `biblioteca_id` parece inadequado aqui. Uma categoria deve ser aplicada a um Jogo.
    -- O campo `id_games` deve ser parte de uma tabela de relacionamento (Categorias_Jogos) ou removido daqui.
    `nm_cat` VARCHAR(255) NOT NULL,
    `desc_cat` VARCHAR(255) NOT NULL
    -- O campo `id_games` foi removido daqui e será tratado na tabela de relacionamento.
);

-- Tabela de Relacionamento N:M entre Jogos e Categorias
CREATE TABLE `Jogos_Categorias`(
    `game_id` BIGINT NOT NULL,
    `cat_id` BIGINT NOT NULL,
    PRIMARY KEY (`game_id`, `cat_id`)
);

-- Tabela Favoritos
CREATE TABLE `Favoritos`(
    `fav_id` BIGINT NOT NULL PRIMARY KEY, -- PK já estava definida
    `id_user` BIGINT NOT NULL,
    `id_games` BIGINT NOT NULL,
    `fav_star` ENUM('1','2','3','4','5', 'indefinido') NOT NULL DEFAULT 'indefinido'
);

-- 1. Tabela Pedidos: FK para Usuários (user_id -> Usuários.id_user)
ALTER TABLE `Pedidos`
ADD CONSTRAINT `fk_pedidos_user` FOREIGN KEY (`user_id`)
REFERENCES `Usuários`(`id_user`);

-- 2. Tabela ItensCompra: FK para Pedidos (order_id -> Pedidos.id_pedido)
ALTER TABLE `ItensCompra`
ADD CONSTRAINT `fk_itenscompra_pedido` FOREIGN KEY (`order_id`)
REFERENCES `Pedidos`(`id_pedido`);

-- 3. Tabela ItensCompra: FK para Jogos (game_id -> Jogos.id_games)
ALTER TABLE `ItensCompra`
ADD CONSTRAINT `fk_itenscompra_jogo` FOREIGN KEY (`game_id`)
REFERENCES `Jogos`(`id_games`);

-- 4. Tabela Biblioteca: FK para Usuários (user_id -> Usuários.id_user)
ALTER TABLE `Biblioteca`
ADD CONSTRAINT `fk_biblioteca_user` FOREIGN KEY (`user_id`)
REFERENCES `Usuários`(`id_user`);

-- 5. Tabela Biblioteca: FK para Jogos (games_id -> Jogos.id_games)
ALTER TABLE `Biblioteca`
ADD CONSTRAINT `fk_biblioteca_jogo` FOREIGN KEY (`games_id`)
REFERENCES `Jogos`(`id_games`);

-- 6. Tabela Favoritos: FK para Usuários (id_user -> Usuários.id_user)
ALTER TABLE `Favoritos`
ADD CONSTRAINT `fk_favoritos_user` FOREIGN KEY (`id_user`)
REFERENCES `Usuários`(`id_user`);

-- 7. Tabela Favoritos: FK para Jogos (id_games -> Jogos.id_games)
ALTER TABLE `Favoritos`
ADD CONSTRAINT `fk_favoritos_jogo` FOREIGN KEY (`id_games`)
REFERENCES `Jogos`(`id_games`);

-- 8. Tabela Jogos_Categorias: FK para Jogos (game_id -> Jogos.id_games)
ALTER TABLE `Jogos_Categorias`
ADD CONSTRAINT `fk_jogoscateg_jogo` FOREIGN KEY (`game_id`)
REFERENCES `Jogos`(`id_games`);

-- 9. Tabela Jogos_Categorias: FK para Categorias (cat_id -> Categorias.id_catg)
ALTER TABLE `Jogos_Categorias`
ADD CONSTRAINT `fk_jogoscateg_cat` FOREIGN KEY (`cat_id`)
REFERENCES `Categorias`(`id_catg`);