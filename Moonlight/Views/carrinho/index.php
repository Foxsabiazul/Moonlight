<?php

// Exemplo de dados fictícios adicionados ao carrinho (normalmente vindo de formulário)

if(!isset($_SESSION["cart_item"])) {
    $_SESSION["cart_item"] = [
        ["code" => "P001", "name" => "Jogo A", "quantity" => 2, "price" => 50.00],
        ["code" => "P002", "name" => "Jogo B", "quantity" => 1, "price" => 70.00]
    ];
}

$total_quantity = 0;
$total_price = 0;
?>
<?php if(!empty($_SESSION["cart_item"])): ?>
<div class="container mt-5">
    <h2 class="Cart2">Carrinho de Compras</h2>
    <table class="table table-bordered">
        <thead>
            <tr class="Clipper">
                <th>Produto</th>
                <th>Código</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Preço Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($_SESSION["cart_item"] as $item):
                $item_total = $item["quantity"] * $item["price"];
                $total_quantity += $item["quantity"];
                $total_price += $item_total;
            ?>
            <tr>
                <td><?= htmlspecialchars($item["name"]) ?></td>
                <td><?= htmlspecialchars($item["code"]) ?></td>
                <td><?= $item["quantity"] ?></td>
                <td>R$ <?= number_format($item["price"], 2, ',', '.') ?></td>
                <td>R$ <?= number_format($item_total, 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" class="text-end"><strong>Total</strong></td>
                <td><strong><?= $total_quantity ?></strong></td>
                <td></td>
                <td><strong>R$ <?= number_format($total_price, 2, ',', '.') ?></strong></td>
            </tr>
        </tbody>
    </table>
<?php else: ?>
    <p class="Pcat">Seu carrinho está vazio.</p>
<?php endif; ?>
</div>
