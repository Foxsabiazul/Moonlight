<?php
$totalGeral = 0;
$itensCarrinho = $itensCarrinho ?? [];
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">Carrinho de Compras</h2>
        </div>
        <div class="card-body">
            <table class="table tabela-carrinho">
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%;">Imagem</th>
                        <th scope="col" style="width: 70%;">Produto</th>
                        <th scope="col" style="width: 20%;" class="text-right">Preço</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if (!empty($itensCarrinho)) {
                        foreach ($itensCarrinho as $item) {
                            $precoItem = $item['preco_unitario'] * $item['quantidade'];
                            $totalGeral += $precoItem;
                            ?>

                            <tr>
                                <td>
                                    <img src="/assets/img/jogos/<?php echo $item['imagem']; ?>"
                                        alt="<?php echo $item['titulo']; ?>"
                                        style="width: 100%; max-width: 80px; height: auto;">
                                </td>

                                <td><?php echo $item['titulo']; ?></td>

                                <td class="text-right">R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                </td>

                            </tr>

                        <?php
                        }
                    } else {

                        ?>
                        <tr>
                            <td colspan="3" class="text-center">Seu carrinho de compras está vazio.</td>
                        </tr>
                    <?php } ?>

                </tbody>

                <tfoot>
                    <tr style="border-top: 2px solid #555;">
                        <td colspan="2" class="text-right"><strong>Valor Total:</strong></td>

                        <td class="text-right"><strong>R$
                                <?php echo number_format($totalGeral, 2, ',', '.'); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-right mt-4 mb-5">
                <a href="/checkout" class="btn btn-lg btn-success">
                    <i class="fas fa-shopping-cart"></i> Finalizar Compra
                </a>
            </div>

        </div>
