-- Desativa temporariamente a verificação de chaves estrangeiras.
-- Isso permite a exclusão em qualquer ordem e previne erros de dependência.
SET FOREIGN_KEY_CHECKS = 0;

-- *********************************************************
-- TRUNCATE TABLES (Ordem Segura: Filhas primeiro, Pais por último)
-- *********************************************************

-- 1. Tabelas de Relacionamento (M:N) e Auditoria (dependem de Jogos, Pedidos, Usuários)
TRUNCATE TABLE `ItensCompra`;
TRUNCATE TABLE `Biblioteca`;
TRUNCATE TABLE `Favoritos`;
TRUNCATE TABLE `AuditoriaPreco`;

-- 2. Tabelas Principais (dependem de Usuários e Categorias)
TRUNCATE TABLE `Pedidos`;
TRUNCATE TABLE `Jogos`;

-- 3. Tabelas Base (sem dependências externas)
TRUNCATE TABLE `Categorias`;
TRUNCATE TABLE `Usuários`;


-- Reativa a verificação de chaves estrangeiras.
SET FOREIGN_KEY_CHECKS = 1;