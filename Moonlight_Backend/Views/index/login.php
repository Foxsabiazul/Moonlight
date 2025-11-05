<div class="login">
    <div class="card">
        <div class="card-header">
            <img src="img/index/Moonlight.png" class="img-fluid logo-image" alt="Moonlight">
        </div>
        <div class="card-body">
            <form class="formUser" name="form1" method="post" data-parsley-validate="">
                <label class="formLabel" for="email">E-mail:</label>
                <input type="email" name="email" class="inputStyle" id="email"
                placeholder="Digite seu email" required
                data-parsley-required-message="Preencha o e-mail"
                data-parsley-type-message="Digite um e-mail válido">
                <br>
                <label class="formLabel" for="senha">Senha:</label>
                <div class="input-group mb-3">
                    <input type="password" name="senha" class="inputStyleGroup" id="senha" 
                    placeholder="Digite sua senha" required
                    data-parsley-required-message="Preencha a senha"
                    data-parsley-errors-container="#erro">
                    <button class="btnStyleGroup" type="button" onclick="mostrarSenha()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="erro"></div>
                <br>
                <button type="submit" class="formBtn w-100">
                    <i class="fas fa-check"></i> Fazer Login
                </button>
            </form>
        </div>
    </div>
</div>