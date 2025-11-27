<?php
$totalGeral = 0;
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">Meus Pedidos</h2>
        </div>
        <div class="card-body" style="max-height:600px; overflow-y:auto">
            <?php
            if (!empty($dadosPedidos)) {
                foreach ($dadosPedidos as $dados) {
            ?>
            <p class="text-left white-text">
                <strong class="text-left white-text">Pedido: <?= $dados->id_pedido ?></strong>
                Data: <?= $dados->dt ?>
                Status atual: <?= $dados->status ?>
            </p> 
            <table class="table table-bordered table-striped">
                <?php
                    $dadosJogos = $this->pedidos->listarItens($dados->id_pedido);
                    $dadosJogos = $dadosJogos ?? [];
                    foreach($dadosJogos as $jogos){
                        ?>
                        <tr>
                            <td style="width: 80%;">Titulo: <?= $jogos->titulo ?></td>
                            <td style="width: 20%;">Preço: R$<?= number_format($jogos->preco, 2, ',', '.'); ?></td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
            <hr>
            <?php
                }
            }else{
                ?>
                    <p class="white-text text-left">
                        Você fez nenhum pedido até o momento.
                    </p>
                <?php
            }
            ?>
        </div>
    </div>
</div>

            
            