<?php
$totalGeral = 0;
$itensCarrinho = isset($_SESSION["carrinho"]) ? $_SESSION["carrinho"] : NULL;
?>

<div class="container">
    <div class="card" style="animation: fadeIn 0.5s ease-in-out;">
        <div class="card-header">
            <h2 class="white-text text-center">Carrinho de Compras</h2>
        </div>
        <div class="card-body">
            <table class="table tabela-carrinho">
                <thead>
                    <tr>
                        <th scope="col" style="width: 10%;">Imagem</th>
                        <th scope="col" style="width: 60%;">Jogo</th>
                        <th scope="col" style="width: 20%;" class="text-right">Preço</th>
                        <th scope="col" style="width: 10%;">Excluir</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $link = "http://localhost/Moonlight/Moonlight_Backend/public";
                    
                    
                    if (!empty($itensCarrinho)) {
                        foreach ($itensCarrinho as $item) {

                            $imagem = isset($item['imagem']) ? $item['imagem'] : 'placeholder_item.jpg';

                            $img = "{$link}/arquivos/{$imagem}";
                            
                            $precoItem = $item['preco'];
                            $totalGeral += $precoItem;
                            ?>

                            <tr>
                                <td>
                                    <img src="<?= $img ?>"
                                        alt="<?php echo $item['titulo']; ?>"
                                        style="width: 100%; max-width: 80px; height: auto;">
                                </td>

                                <td><?php echo $item['titulo']; ?></td>

                                <td class="text-right">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?>
                                </td>

                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>/carrinho/excluir/<?= $item['id_games'] ?>" class="styledBtn p-x1 black-text mr-x1">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>

                            </tr>

                        <?php
                        }
                    } else {

                        ?>
                        <tr>
                            <td colspan="4" class="text-center">Seu carrinho de compras está vazio.</td>
                        </tr>
                    <?php } ?>

                </tbody>

                <tfoot>
                    <tr style="border-top: 2px solid #555;">
                        <td colspan="3" class="text-right"><strong>Valor Total:</strong></td>

                        <td class="text-right"><strong>R$
                                <?php echo number_format($totalGeral, 2, ',', '.'); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-right mt-4 mb-5">
                <a href="<?= BASE_URL ?>/carrinho/limpar" class="styledBtn add-cart-btn">Limpar Carrinho</a>
                <a href="<?= BASE_URL ?>/carrinho/checkout" class="styledBtn buy-now-btn">
                    <i class="fas fa-shopping-cart"></i> Finalizar Compra
                </a>
            </div>

        </div>
