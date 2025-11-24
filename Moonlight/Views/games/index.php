<?php
    if (!empty($dadosJogo)) {
        $id_jogo = htmlspecialchars($dadosJogo->id_games);
        // Verifique se o jogo está no carrinho
        $estaNoCarrinho = isset($_SESSION["carrinho"][$id_jogo]);
        $link_backend = $link;
    }
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">
                <?= htmlspecialchars($dadosJogo ? $dadosJogo->titulo : $tituloJogo) ?>
            </h2>
        </div>
        
        <div class="card-body game-details-body">

            <?php if (!empty($dadosJogo)): 
                $imagem = isset($dadosJogo->imagem) ? $dadosJogo->imagem : 'placeholder_item.jpg';
                $imgSrc = "{$link}/arquivos/{$imagem}";
            ?>

            <div class="row">
                
                <div class="col-12 col-lg-8 text-center mb-4">
                    <img src="<?= $imgSrc ?>" alt="Capa do Jogo <?= htmlspecialchars($dadosJogo->titulo) ?>" class="game-detail-img">
                </div>
                
                <div class="col-12 col-lg-4 white-text">
                    
                    <h1 class="price-text">
                        R$ <?= number_format($dadosJogo->preco, 2, ",", ".") ?>
                    </h1>
                    
                    <h3 class="mt-4">Descrição:</h3>
                    <p class="description-text">
                        <?= nl2br(htmlspecialchars($dadosJogo->descricao)) ?>
                    </p>
                    
                    <?php if (isset($dadosJogo->data_lancamento)): ?>
                        <p class="release-date-text">
                            **Lançamento:** <?= htmlspecialchars($dadosJogo->data_lancamento) ?>
                        </p>
                    <?php endif; ?>

                    <div class="d-flex flex-column flex-md-row mt-5 button-group">
                        
                        <a href="<?= BASE_URL ?>/carrinho/adicionar/<?= $id_jogo ?>?redirect=carrinho"
                            class="styledBtn buy-now-btn">
                            Comprar Agora
                        </a>
                        
                        <?php if ($estaNoCarrinho): ?>

                        <a href="<?= BASE_URL ?>/carrinho/excluir/<?= $id_jogo ?>?redirect=detalhes"
                        class="styledBtn remove-cart-btn ml-md-3 mt-3 mt-md-0 btn-danger">
                            Remover do Carrinho
                        </a>
                        
                    <?php else: ?>

                        <a href="<?= BASE_URL ?>/carrinho/adicionar/<?= $id_jogo ?>"
                        class="styledBtn add-cart-btn ml-md-3 mt-3 mt-md-0">
                            Adicionar ao Carrinho
                        </a>
                        
                    <?php endif; ?>

                    </div>
                    
                </div>
            </div>

            <?php else: ?>
                <p class="white-text text-center">
                    Não foi possível carregar os detalhes do jogo ou o jogo não existe.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>