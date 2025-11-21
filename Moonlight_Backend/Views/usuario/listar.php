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
            <h2 class="float-start as-center white-text">Lista de Usuários</h2>
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
            <?php
            $dadosUsuario = $this->usuario->listarUsuario() ?? NULL;
            if($dadosUsuario): ?>
            <p class="white-text">Abaixo os usuários cadastrados:</p>
            <div class="table-responsive-scroll">
                <table class="table table-bordered table-striped dashboard-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Nome do Usuário</td>
                            <td>E-mail</td>
                            <td>Data_Criação</td>
                            <td>Opções</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($dadosUsuario as $dados) {
                                ?>
                                <tr>
                                    <td><?=$dados->id_user?></td>
                                    <td><?=$dados->nm_user?></td>
                                    <td><?=$dados->email?></td>
                                    <td><?=$dados->data_criacao?></td>
                                    <td width="150px" id="opcoes">
                                        <div class="text-center">
                                            <a href="javascript:confirmarExclusao(event, <?=$dados->id_user?>, 'usuario')" class="formBtn p-x1 black-text mr-x1">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/usuario/index/<?=$dados->id_user?>" class="formBtn p-x1 black-text ml-x1">
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
                <h3 class="white-text">Não há usuarios cadastrados, cadastre-os</h3>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>