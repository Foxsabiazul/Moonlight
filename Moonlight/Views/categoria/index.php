<div class="container">

    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">
                <?= htmlspecialchars($tituloCategoria) ?>
            </h2>
        </div>
        <div class="card-body">
            <div class="row card-deck">
            <?php 
                //carregar o conteudo dos Jogos da API

                foreach($dadosJogos as $dados) {

                    $img = "{$link}/arquivos/{$dados->imagem}";
                    // http://localhost/Moonlight/Moonlight_Backend/public/arquivos/13123213412.jpg

                    ?>
                    <div class="card-item col-12 col-md-6 text-center">   
                        <div class="card">
                            <img src="<?= $img ?>" class="cardImg">
                            <div class="card-content">
                                <p class="white-text">
                                    <?= $dados->titulo ?>
                                </p>
                                <p class="white-text">
                                    R$ 
                                    <?= number_format($dados->preco,2,",",".") ?>
                                </p>
                                <p class="card-button-wrapper">
                                    <a href="<?= BASE_URL ?>/games/index/<?= $dados->id_games ?>"
                                    class="styledBtn">
                                        Detalhes do jogo
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            ?>
            </div>
        </div>
    </div>
</div>