<div class="container">

    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">Jogos de <?= $dadosCategoria->nm_cat ?>:</h2>
        </div>
        <div class="card-body">
            <div class="row">
            <?php 
                //carregar o conteudo dos Jogos da API

                foreach($dadosJogos as $dados) {

                    $img = "{$link}/arquivos/{$dados->imagem}";

                    ?>
                    <div class="col-12 col-md-4 text-center">
                        <div class="card">
                            <img src="<?= $img ?>" class="w-100 cardImg">
                            <br>
                            <p class="white-text">
                                <?= $dados->titulo ?>
                            </p>
                            <p class="white-text">
                                R$ 
                                <?= number_format($dados->preco,2,",",".") ?>
                            </p>
                            <br>
                            <p>
                                <a href="<?= BASE_URL ?>/games/index/<?= $dados->id_games ?>"
                                class="styledBtn">
                                    Detalhes do jogo
                                </a>
                            </p>
                        </div>
                    </div>
                    <?php
                }
            ?>
            </div>
        </div>
    </div>
</div>