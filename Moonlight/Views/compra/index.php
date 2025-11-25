<?php

?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="checkout-header-container">
                <h2 class="white-text text-center"><?= $msgTitle ?></h2>
                <img src="<?= BASE_URL ?>/img/gateway/mercado-pago-logo.png" style="width: 500px;" alt="Mercado Pago" class="as-center">
            </div>
        </div>
        <div class="card-body">
            <p class="text-center white-text">
                <!-- Botão de pagamento -->
                <?= $msgParagraph ?>
            </p>
            <div class="text-center mt-4">
                <a href="<?= BASE_URL ?>/pedidos" class="styledBtn buy-now-btn mr-x1">
                    Ver Meus Pedidos
                </a>
                <a href="<?= BASE_URL ?>/" class="styledBtn add-cart-btn ml-x1">
                    Continuar Comprando
                </a>
            </div>
        </div>
    </div>
</div>