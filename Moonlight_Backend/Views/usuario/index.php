<div class="container">
    <div class="card">
        <div class="card-header jc-between">
            <?php if(!empty($dados->id_user)): ?>
            <h2 class="float-start as-center white-text">Atualização de Usuário</h2>
            <?php else: ?>
            <h2 class="float-start as-center white-text">Cadastro de Usuário</h2>
            <?php endif; ?>
            <div class="float-end">
                <a href="<?= BASE_URL ?>/usuario" title="Novo Registro" class="simpleBtn p-x1 textdec-Debloat white-text">
                    <i class="fas fa-file"></i> Novo Registro
                </a>

                <a href="<?= BASE_URL ?>/usuario/listar" title="Listar" class="simpleBtn p-x1 textdec-Debloat white-text">
                    <i class="fas fa-file"></i> Listar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form name="formCadastro" method="post" action="<?= BASE_URL ?>/usuario/salvar" data-parsley-validate="">
                <div class="row">
                    <div class="col-12 col-md-1">
                        <label class="formLabel" for="id">ID:</label>
                        <input type="text" name="id" id="id" class="inputForm-control" readonly value="<?= $id ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="formLabel" for="nome">Nome do Usuário:</label>
                        <input type="text" name="nome" id="nome" required class="inputForm-control" value="<?= $nome ?>"
                            data-parsley-required-message="Preencha o nome" placeholder="Digite o nome do Usuario">
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="formLabel" for="email">E-mail do Usuário:</label>
                        <input type="email" name="email" id="email" required class="inputForm-control" value="<?= $email ?>"
                            data-parsley-required-message="Preencha o e-mail"
                            data-parsley-type-message="Digite um e-mail válido" placeholder="Digite o Email do Usuario">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 col-md-4">
                        <label class="formLabel" for="senha">Senha:</label>
                        <input type="password" name="senha" id="senha" class="inputForm-control" placeholder="Digite a Senha do Usuario">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="formLabel" for="senha2">Redigite a senha:</label>
                        <input type="password" name="senha2" id="senha2"  class="inputForm-control"
                            data-parsley-equalto="#senha"
                            data-parsley-equalto-message="As senhas são diferentes"
                            placeholder="Redigite a senha do Usuario">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="tipo" class="formLabel">Tipo do Usuario</label>
                        <select name="tipo" id="tipo" class="selectForm-control" data-parsley-required-message="Selecione um tipo" required>
                            <option value="">Selecione um Tipo</option>
                            <option value="cliente" <?= $tipo === "cliente" ? "selected" : "" ?>>Cliente</option>
                            <option value="admin" <?= $tipo === "admin" ? "selected" : "" ?>>Administrador</option>
                        </select>
                    </div>
                </div>
                <br>
                <button type="submit" class="formBtn p-x1 black-text float-end">
                    <i class="fas fa-check"></i> Salvar Registro!
                </button>
            </form>
        </div>
    </div>
</div>