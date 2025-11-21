<div id="modalContainer" class="modal-container" style="display: none; opacity: 0;">
    <div class="modal">
        <div class="modalContainerTitle">
            <h3 class="modalTitle" id="modalTitle">
            </h3>
        </div>
        <div class="modalContainerMessage">
            <p class="modalMessage" id="modalMessage">
            </p>
        </div>
        <div class="modalDualButton">
            <button id="btnCancelar">Cancelar</button>
            <button id="btnConfirmar">Excluir</button>
        </div>
    </div>
</div>
<div id="modalOverlay" class="modal-overlay" style="display: none; opacity: 0;"></div>
<div class="container">
    <div class="card">
        <div class="card-header jc-between">
            <h2 class="float-start as-center white-text">Lista de Categorias</h2>
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
            <?php
            $dadosCategoria = $this->categoria->listarCategoria() ?? null;
            if($dadosCategoria): ?>
            <p class="white-text">Abaixo as Categorias cadastradas:</p>
            <div class="table-responsive-scroll">
                <table class="table table-bordered table-striped dashboard-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Nome da Categoria</td>
                            <td>Descricao</td>
                            <td>Opções</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($dadosCategoria as $dados) {
                                ?>
                                <tr>
                                    <td><?=$dados->id_categoria?></td>
                                    <td><?=$dados->nm_cat?></td>
                                    <td><?=$dados->desc_cat?></td>
                                    <td width="150px" id="opcoes">
                                        <div class="text-center">
                                            <a href="javascript:confirmarExclusao(event, <?=$dados->id_categoria?>, 'categoria')" class="formBtn p-x1 black-text mr-x1">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/categoria/index/<?=$dados->id_categoria?>" class="formBtn p-x1 black-text ml-x1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
                <?php else: ?>
                <h4 class="white-text">Não há categorias cadastradas, cadastre-as</h4>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>