
<div class="container">
    <div class="card">
        <div class="card-header jc-between">
            <?php if(!empty($dados->id_games)): ?>
            <h2 class="float-start as-center white-text">Atualização de Jogos</h2>
            <?php else: ?>
            <h2 class="float-start as-center white-text">Cadastro de Jogos</h2>
            <?php endif; ?>
            <div class="float-end">
                <a href="<?= BASE_URL ?>/games" title="Novo Registro" class="simpleBtn p-x1 textdec-Debloat white-text">
                    <i class="fas fa-file"></i> Novo Registro
                </a>

                <a href="<?= BASE_URL ?>/games/listar" title="Listar" class="simpleBtn p-x1 textdec-Debloat white-text">
                    <i class="fas fa-file"></i> Listar
                </a>
            </div>
        </div>
        <div class="card-body">
            <form name="formCadastro" method="post" action="<?= BASE_URL ?>/games/salvar" data-parsley-validate="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12 col-md-1">
                        <label class="formLabel" for="id_games">ID:</label>
                        <input type="text" name="id_games" id="id_games" class="inputForm-control" readonly value="<?= $id_games ?>">
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="formLabel" for="id_categoria">Categoria do Jogo:</label>
                        <?php 
                            $dadosCategoria = $this->categoria->listarCategoria() ?? null; 
                            if(!$dadosCategoria){
                                $gambiarraInsanaPraUrlPegar = BASE_URL;
                                echo "<a href='{$gambiarraInsanaPraUrlPegar}/categoria' class='simpleBtn p-x1 white-text'>Você ainda não criou alguma categoria, faça agora.</a>";
                            }
                        ?>
                        <select name="id_categoria" id="id_categoria" class="selectForm-control" data-parsley-required-message="Selecione uma Categoria" <?= $dadosDisponiveis = $dadosCategoria ? "" : "style='display:none'" ?> required>
                            <?php
                                
                                if($dadosCategoria){
                                    echo "<option value=''>Selecione uma Categoria</option>";
                                    foreach($dadosCategoria as $dados) {
                                        $selectedOption = ($id_categoria == $dados->id_categoria) ? "selected" : "";
                                        echo "<option value='{$dados->id_categoria}' {$selectedOption} >{$dados->nm_cat}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="formLabel" for="titulo">Titulo do Jogo:</label>
                        <input type="text" name="titulo" id="titulo" required class="inputForm-control" value="<?= $titulo ?>"
                            data-parsley-required-message="Preencha o titulo" placeholder="Digite o titulo do Jogo">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <label class="formLabel" for="descricao">Descrição do Jogo:</label>
                        <textarea name="descricao" id="descricao" class="inputForm-control"
                        placeholder="Digite uma Descrição sobre o Jogo" 
                        rows="5" style="resize: none;"><?= $descricao ?></textarea>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label class="formLabel" for="preco">Preço do Jogo:</label>
                        <input type="text" name="preco" id="preco" class="inputForm-control"
                        placeholder="Digite o preço do Jogo"
                        required data-parsley-required-message="Preencha o preço"
                        value="<?= number_format($preco,2,",",".") ?>"
                        inputmode="numeric">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="formLabel" for="imagem">Selecionar uma Imagem:</label>
                        <input type="file" name="imagem" id="imagem" class="inputForm-control"
                        accept="jpg">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <label class="formLabel" for="link">Link do Jogo:</label>
                        <input type="text" name="link" id="link" class="inputForm-control"
                        placeholder="Digite o link do Jogo (para instalação)"
                        value="<?= $link ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="formLabel" for="data_lancamento">Data de lançamento:</label>
                        <input type="date" name="data_lancamento" id="data_lancamento" class="inputForm-control"
                        placeholder="Digite a data de lançamento do jogo"
                        required data-parsley-required-message="Preencha a data de lançamento"
                        value="<?= $data ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="ativo" class="formLabel">Jogo está ativo:</label>
                        <select name="ativo" id="ativo" class="selectForm-control" data-parsley-required-message="Selecione se o jogo já estará ativo." required>
                            <option value="">Selecione uma opção.</option>
                            <option value="S" <?= $ativo === "S" ? "selected" : "" ?>>Sim</option>
                            <option value="N" <?= $ativo === "N" ? "selected" : "" ?>>Não</option>
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
<script>


    $(function() {
        $('#preco').maskMoney({
            thousands:'.', 
            decimal:','
        });
    });
</script>