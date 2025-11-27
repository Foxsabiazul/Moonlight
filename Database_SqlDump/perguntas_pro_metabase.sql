
--financeiro

--Faturamento Total Bruto
	SELECT SUM(total) FROM Pedidos WHERE status = 'aprovado';
--Faturamento Líquido Estimado
    SELECT SUM(total * 0.9501) FROM Pedidos WHERE status = 'aprovado';
-- Total de Pedidos Aprovados	
    SELECT COUNT(id_pedido) FROM Pedidos WHERE status = 'aprovado';
-- Evolução do Faturamento por Mês	
    SELECT DATE_FORMAT(data_pedido, '%Y-%m') AS Mês, SUM(total) AS Total_Vendido FROM Pedidos WHERE status = 'aprovado' GROUP BY Mês ORDER BY Mês;

--catalogo


--Total de Jogos na Loja (Ativos)
    SELECT COUNT(id_games) FROM Jogos WHERE ativo = 'S';
--Jogos Mais Vendidos (Top 10)
    SELECT J.titulo, COUNT(IC.id_games) AS Total_Vendido FROM ItensCompra AS IC JOIN Pedidos AS P ON IC.id_pedido = P.id_pedido JOIN Jogos AS J ON IC.id_games = J.id_games WHERE P.status = 'aprovado' GROUP BY J.titulo ORDER BY Total_Vendido DESC LIMIT 10;
--Inventário por Categoria
    SELECT C.nm_cat, COUNT(J.id_games) FROM Jogos AS J JOIN Categorias AS C ON J.id_categoria = C.id_categoria GROUP BY C.nm_cat;

--comunidade


--Total de Usuários Cadastrados	
    SELECT COUNT(id_user) FROM Usuários;
--Usuários com Compras (Clientes Ativos)	
    SELECT COUNT(DISTINCT id_user) FROM Pedidos WHERE status = 'aprovado';
--Novos Usuários por Mês
	SELECT DATE_FORMAT(data_criacao, '%Y-%m') AS Mês, COUNT(id_user) AS Novos_Usuários FROM Usuários GROUP BY Mês ORDER BY Mês;