
--financeiro

--Faturamento Total Bruto
	SELECT SUM(p.total) FROM Pedidos p inner join usuários u on u.id_user = p.id_user WHERE p.status = 'aprovado' and u.tipo = 'cliente';
--Faturamento Líquido Estimado
    SELECT SUM(p.total * 0.9501) FROM Pedidos p inner join usuários u on u.id_user = p.id_user WHERE p.status = 'aprovado' and u.tipo = 'cliente';
-- Total de Pedidos Aprovados	
    SELECT COUNT(p.id_pedido) FROM Pedidos p inner join usuários u on u.id_user = p.id_user WHERE p.status = 'aprovado' and u.tipo = 'cliente';
-- Evolução do Faturamento liquido por Mês	
    SELECT DATE_FORMAT(p.data_pedido, '%Y-%m') AS Mês, SUM(p.total * 0.9501) AS Total_Vendido FROM Pedidos p inner join usuários u on u.id_user = p.id_user WHERE p.status = 'aprovado' and u.tipo = 'cliente' GROUP BY Mês ORDER BY Mês;

--catalogo


--Total de Jogos na Loja (Ativos)
    SELECT COUNT(id_games) FROM Jogos WHERE ativo = 'S';
--Jogos Mais Vendidos (Top 10)
    SELECT J.titulo, COUNT(IC.id_games) AS Total_Vendido FROM ItensCompra AS IC JOIN Pedidos AS P ON IC.id_pedido = P.id_pedido JOIN Jogos AS J ON IC.id_games = J.id_games JOIN Usuários AS U ON U.id_user = P.id_pedido WHERE P.status = 'aprovado' AND U.tipo = "cliente" GROUP BY J.titulo ORDER BY Total_Vendido DESC LIMIT 10;
--Inventário por Categoria
    SELECT C.nm_cat, COUNT(J.id_games) FROM Jogos AS J JOIN Categorias AS C ON J.id_categoria = C.id_categoria GROUP BY C.nm_cat;

--comunidade


--Total de Usuários Cadastrados	
    SELECT COUNT(u.id_user) FROM Usuários u WHERE u.tipo = 'cliente';
--Usuários com Compras (Clientes Ativos)	
    SELECT COUNT(DISTINCT p.id_user) FROM Pedidos p inner join usuários u on u.id_user = p.id_user WHERE p.status = 'aprovado' and u.tipo = 'cliente';
--Novos Usuários por Mês
    SELECT DATE_FORMAT(u.data_criacao, '%Y-%m') AS Mês, COUNT(u.id_user) AS Novos_Usuários FROM Usuários u WHERE u.tipo = "cliente" GROUP BY Mês ORDER BY Mês;