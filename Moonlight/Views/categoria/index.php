<div class="container">

    <div class="card" style="animation: fadeIn 0.5s ease-in-out;">
        <div class="card-header">
            <h2 class="white-text text-center">
                <?= htmlspecialchars($tituloCategoria) ?>
            </h2>
        </div>
        <div class="card-body">
            <div id="lista-de-jogos" class="row card-deck">
                <?php 
                    // Variável para rastrear se todos os jogos foram carregados
                    $totalCarregados = count($dadosJogos);
                    $limitePorPagina = 8; // a API usa limite de 8 jogos
                    
                    foreach($dadosJogos as $dados) {

                        $imagem = isset($dados->imagem) ? $dados->imagem : 'placeholder_item.jpg';

                        $img = "{$link}/arquivos/{$imagem}";

                        ?>
                        <div id="card-item" class="card-item col-12 col-md-6 text-center">   
                            <div id="card" class="card">
                                <img id="cardImg" src="<?= $img ?>" class="cardImg">
                                <div id="card-content" class="card-content">
                                    <p id="titulo" class="white-text">
                                        <?= $dados->titulo ?>
                                    </p>
                                    <p id="preco" class="white-text">
                                        R$ 
                                        <?= number_format($dados->preco,2,",",".") ?>
                                    </p>
                                    <p id="card-button-wrapper" class="card-button-wrapper">
                                        <a id="styledBtn" href="<?= BASE_URL ?>/games/index/<?= $dados->id_games ?>"
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
                
                <div class="text-center mt-4">
                    <button 
                        id="btn-carregar-mais" 
                        class="styledBtn w-50"
                        data-next-page="2"
                        <?php if($id): ?>data-categoria-id="<?= htmlspecialchars($id) ?>" <?php endif; ?>
                        <?php if ($totalCarregados < $limitePorPagina): ?> style="display: none;" <?php endif; ?>
                    >
                        Carregar Mais Jogos
                    </button>
                    <p id="status-carregamento" class="white-text mt-2"></p>
                </div>
            </div>
        </div>
    </div>
</div>