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

--      Dentro de GamesModel na MoonlightBackend COALESCE(@usuario_logado, USER())
--        $usuarioLogado = $_SESSION['Logado_Na_Sessão']['nm_user'] ?? 'Sistema Desconhecido';
--
--        Prepare e execute o comando SET @usuario_logado

--        $sqlSetUser = "SET @usuario_logado = :usuario_logado";
--        $stmtSetUser = $this->pdo->prepare($sqlSetUser);
--        $stmtSetUser->bindParam(":usuario_logado", $usuarioLogado);
--        $stmtSetUser->execute();