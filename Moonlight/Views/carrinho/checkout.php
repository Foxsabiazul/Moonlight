<?php

?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="checkout-header-container">
                <h2 class="white-text text-center">Checkout da Compra</h2>
                <img src="<?= BASE_URL ?>/img/gateway/mercado-pago-logo.png" style="width: 350px;" alt="Mercado Pago" class="as-center img-fluid logo-image">
            </div>
        </div>
        <div class="card-body">
            <p class="text-center">
                <!-- Botão de pagamento -->
                <script src="https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js"
                        data-preference-id="<?php echo $preference->id; ?>"
                        data-button-label="Pagar com Mercado Pago (Boleto, Cartão de Crédito ou Débito)">
                </script>
            </p>
        </div>
    </div>
</div>