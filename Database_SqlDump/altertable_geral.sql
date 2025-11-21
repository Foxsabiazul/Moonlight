-- *********************************************************
-- 1. TABELA AUDITORIAPRECO (JOGOS -> AUDITORIAPRECO)
-- Regra: Se um Jogo for excluído, seu histórico de preços SOME. (CASCADE)
-- *********************************************************
ALTER TABLE `AuditoriaPreco`
DROP FOREIGN KEY `fk_auditoria_games`;

ALTER TABLE `AuditoriaPreco`
ADD CONSTRAINT `fk_auditoria_games`
FOREIGN KEY (`id_games`)
REFERENCES `Jogos` (`id_games`)
ON DELETE CASCADE;

---

-- *********************************************************
-- 2. TABELA PEDIDOS (USUÁRIOS -> PEDIDOS)
-- Regra: Se o Usuário for excluído, todos os seus Pedidos SOMEM. (CASCADE)
-- *********************************************************
ALTER TABLE `Pedidos`
DROP FOREIGN KEY `fk_pedidos_user`;

ALTER TABLE `Pedidos`
ADD CONSTRAINT `fk_pedidos_user` FOREIGN KEY (`id_user`)
    REFERENCES `Usuários`(`id_user`)
    ON DELETE CASCADE;

---

-- *********************************************************
-- 3. TABELA BIBLIOTECA (USUÁRIOS -> BIBLIOTECA e JOGOS -> BIBLIOTECA)
-- Regra: Se o Usuário some ou o Jogo some, a entrada na Biblioteca SOME. (CASCADE)
-- *********************************************************
ALTER TABLE `Biblioteca`
DROP FOREIGN KEY `fk_biblioteca_user`;

ALTER TABLE `Biblioteca`
ADD CONSTRAINT `fk_biblioteca_user` FOREIGN KEY (`id_user`)
    REFERENCES `Usuários`(`id_user`)
    ON DELETE CASCADE;

ALTER TABLE `Biblioteca`
DROP FOREIGN KEY `fk_biblioteca_jogo`;

ALTER TABLE `Biblioteca`
ADD CONSTRAINT `fk_biblioteca_jogo` FOREIGN KEY (`id_games`)
    REFERENCES `Jogos`(`id_games`)
    ON DELETE CASCADE;

---

-- *********************************************************
-- 4. TABELA ITENSCOMPRA (PEDIDOS -> ITENSCOMPRA e JOGOS -> ITENSCOMPRA)
-- Regra Pedidos: Se o Pedido for excluído, seus Itens SOMEM. (CASCADE)
-- Regra Jogos: Se o Jogo for excluído, a exclusão é RESTRITA se houver item de compra. (RESTRICT)
-- *********************************************************
ALTER TABLE `ItensCompra`
DROP FOREIGN KEY `fk_itenscompra_pedido`;

ALTER TABLE `ItensCompra`
ADD CONSTRAINT `fk_itenscompra_pedido` FOREIGN KEY (`id_pedido`)
    REFERENCES `Pedidos`(`id_pedido`)
    ON DELETE CASCADE;

ALTER TABLE `ItensCompra`
DROP FOREIGN KEY `fk_itenscompra_jogo`;

ALTER TABLE `ItensCompra`
ADD CONSTRAINT `fk_itenscompra_jogo` FOREIGN KEY (`id_games`)
    REFERENCES `Jogos`(`id_games`)
    ON DELETE RESTRICT;

---

-- *********************************************************
-- 5. TABELA FAVORITOS (USUÁRIOS -> FAVORITOS e JOGOS -> FAVORITOS)
-- Regra: Se o Usuário some ou o Jogo some, a entrada nos Favoritos SOME. (CASCADE)
-- *********************************************************
ALTER TABLE `Favoritos`
DROP FOREIGN KEY `fk_favoritos_jogo`;

ALTER TABLE `Favoritos`
ADD CONSTRAINT `fk_favoritos_jogo` FOREIGN KEY (`id_games`)
    REFERENCES `Jogos`(`id_games`)
    ON DELETE CASCADE;

ALTER TABLE `Favoritos`
DROP FOREIGN KEY `fk_favoritos_user`;

ALTER TABLE `Favoritos`
ADD CONSTRAINT `fk_favoritos_user` FOREIGN KEY (`id_user`)
    REFERENCES `Usuários`(`id_user`)
    ON DELETE CASCADE;

---

-- *********************************************************
-- 6. TABELA JOGOS (CATEGORIAS -> JOGOS)
-- Regra: Se a Categoria for excluída, todos os Jogos dessa categoria SOMEM. (CASCADE)
-- *********************************************************
ALTER TABLE `Jogos`
DROP FOREIGN KEY `fk_jogos_categoria`;

ALTER TABLE `Jogos`
ADD CONSTRAINT `fk_jogos_categoria` FOREIGN KEY (`id_categoria`)
    REFERENCES `Categorias`(`id_categoria`)
    ON DELETE CASCADE;