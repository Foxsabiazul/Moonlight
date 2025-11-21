<div class="container">

    <div class="card">
        <div class="card-header">
            <div class="account-info-container">
                <h2 class='white-text text-center p-x1'>Olá, <?= htmlspecialchars($userName) ?></h2>
                <div class="account-header-info-content">
                    <div class="account-header-info-data">
                        <span>
                            <i class="fa-solid fa-user"></i> <?= htmlspecialchars($userName) ?>
                        </span>
                        <span>
                            <i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($email) ?>
                        </span>
                        <?php if($_SESSION['Logado_Na_Sessão']['tipo'] == 'admin'): ?>
                        <span>
                            <i class="fa-solid fa-hammer"></i> Administrador
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h2 class='white-text text-center p-x1'>Minha Conta:</h2>
            <form class="formUser" action="<?= BASE_URL ?>/usuario/atualizarInformacoesPessoais" method="post">
                <h3 class="white-text text-center p-x1">Informações Pessoais</h3>
                    <label class="formLabel" for="nome">Nome:</label>
                    <input type="nome" name="nome" class="inputStyle" id="nome"
                    placeholder="Digite seu nome" required
                    data-parsley-required-message="Preencha o nome"
                    value="<?= htmlspecialchars($userName) ?>">
                    <br>
                    <label class="formLabel" for="email">E-mail:</label>
                    <input type="email" name="email" class="inputStyle" id="email"
                    placeholder="Digite seu email" required
                    data-parsley-required-message="Preencha o e-mail"
                    data-parsley-type-message="Digite um e-mail válido"
                    value="<?= htmlspecialchars($email) ?>">
                    <br>
                    <label class="formLabel" for="senha">Senha:</label>
                    <div class="input-group">
                        <input type="password" name="senha" class="inputStyleGroup" id="senha" 
                        placeholder="Digite sua senha OU Altere-a" required
                        data-parsley-required-message="Preencha a senha"
                        data-parsley-errors-container="#erro">
                        <button class="btnStyleGroup" type="button" onclick="mostrarSenha()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <br>
                    <div id="erro"></div>
                    <br>
                    <label class="formLabel" for="senha2">Redigite a senha:</label>
                    <input type="password" name="senha2" id="senha2" class="inputStyle"
                        required
                        data-parsley-required-message="Preencha igual a senha acima"
                        data-parsley-equalto="#senha"
                        data-parsley-equalto-message="As senhas são diferentes"
                        placeholder="Redigite a senha acima">
                    <br>
                <button type="submit" class="formBtn w-100">
                    <i class="fas fa-check"></i> Salvar Alterações
                </button>
            </form>
        </div>
    </div>
</div>