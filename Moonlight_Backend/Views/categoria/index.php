<div class="container">
    <div class="card">
        <div class="card-header jc-between">
            <?php if(!empty($dados->id_categoria)): ?>
            <h2 class="float-start as-center white-text">Atualização de Categoria</h2>
            <?php else: ?>
            <h2 class="float-start as-center white-text">Cadastro de Categoria</h2>
            <?php endif; ?>
            <div class="float-end">
                <a href="<?= BASE_URL ?>/categoria" title="Novo Registro" class="simpleBtn p-x1 textdec-Debloat white-text">
                    <i class="fas fa-file"></i> Novo Registro
                </a>

                <a href="<?= BASE_URL ?>/categoria/listar" title="Listar" class="simpleBtn p-x1 textdec-Debloat white-text">
                    <i class="fas fa-file"></i> Listar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form name="formCadastro" method="post" action="<?= BASE_URL ?>/categoria/salvar" data-parsley-validate="">
                <div class="row">
                    <div class="col-12 col-md-1">
                        <label class="formLabel" for="id">ID:</label>
                        <input type="text" name="id" id="id" class="inputForm-control" readonly value="<?= $id ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="formLabel" for="nome">Nome da Categoria:</label>
                        <input type="text" name="nome" id="nome" required class="inputForm-control" value="<?= $nome ?>"
                            data-parsley-required-message="Preencha o nome da Categoria" placeholder="Digite o nome da Categoria">
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="formLabel" for="descricao">Descrição da Categoria:</label>
                        <input type="text" name="descricao" id="descricao" required class="inputForm-control" value="<?= $descricao ?>"
                            data-parsley-required-message="Preencha a descrição" placeholder="Digite a descricao da Categoria">
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