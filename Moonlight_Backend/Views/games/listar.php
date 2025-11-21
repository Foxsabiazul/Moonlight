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
            <h2 class="float-start as-center white-text">Lista de Jogos</h2>
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
            <?php
                $dadosGames = $this->games->listarGames() ?? null;
                if($dadosGames): ?>
            <p class="white-text">Abaixo os jogos cadastrados:</p>
            <div class="table-responsive-scroll">
                <table class="table table-bordered table-striped dashboard-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Categ. do Jogo</td>
                            <td>Nome do Jogo</td>
                            <td>Preço</td>
                            <td>Imagem</td>
                            <td>Ativo</td>
                            <td>Opções</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($dadosGames as $dados) {
                                $imagem = isset($dados->imagem) ? $dados->imagem : 'placeholder_item.jpg';

                                ?>
                                <tr>
                                    <td><?=$dados->id_games?></td>
                                    <td><?=$dados->nm_cat?></td>
                                    <td><?=$dados->titulo?></td>
                                    <td>R$<?=number_format($dados->preco,2,",",".")?></td>
                                    <td><img src="<?= BASE_URL ?>/arquivos/<?=$imagem?>" width="100px"></td>
                                    <td><?php
                                        if($dados->ativo === 'S'){
                                            echo 'Sim';
                                        }else{
                                            echo 'Não';
                                        }
                                    ?>
                                    </td>
                                    <td width="150px" id="opcoes">
                                        <div class="text-center">
                                            <a href="javascript:confirmarExclusao(event, <?=$dados->id_games?>, 'games')" class="formBtn p-x1 black-text mr-x1">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/games/index/<?=$dados->id_games?>" class="formBtn p-x1 black-text ml-x1">
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
                <h4 class="white-text">Não há jogos cadastrados, cadastre-os</h4>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>