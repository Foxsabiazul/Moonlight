CREATE or Replace TRIGGER `trg_auditar_preco_jogo`
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
END