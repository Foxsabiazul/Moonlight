<div class="signup">
    <div class="card" style="animation: fadeIn 0.5s ease-in-out;">
        <div class="card-header">
            <img src="<?= BASE_URL ?>/img/index/Moonlight.png" class="img-fluid logo-image" alt="Moonlight">
        </div>
        <div class="card-body">
            <form class="formUser" name="form1" method="post" action="<?= BASE_URL ?>/usuario/salvar" data-parsley-validate="">
                <h4 class="formLabel text-center">Cadastre-se</h4>
                <br>
                <label class="formLabel" for="nome">Nome do Usuário:</label>
                <input type="text" name="nome" id="nome" required class="inputStyle"
                    data-parsley-required-message="Preencha o nome" placeholder="Digite seu nome">
                <br>
                <label class="formLabel" for="email">E-mail:</label>
                <input type="email" name="email" class="inputStyle" id="email"
                placeholder="Digite seu email" required
                data-parsley-required-message="Preencha o e-mail"
                data-parsley-type-message="Digite um e-mail válido">
                <br>
                <label class="formLabel" for="senha">Senha:</label>
                <div class="input-group">
                    <input type="password" name="senha" class="inputStyleGroup" id="senha" 
                    placeholder="Digite sua senha" required
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
                    data-parsley-required-message="Preencha para validar senha"
                    data-parsley-equalto="#senha"
                    data-parsley-equalto-message="As senhas são diferentes"
                    placeholder="Redigite sua senha">
                <br>
                <button type="submit" class="formBtn w-100">
                    <i class="fas fa-check"></i> Finalizar Cadastro!
                </button>
                <br>
                <a class="simpleBtn as-center" href="<?= BASE_URL ?>/usuario/access">Já possui conta?</a>
            </form>
        </div>
    </div>
</div>