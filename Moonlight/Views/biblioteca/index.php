<div class="container">
    <div class="card" style="animation: fadeIn 0.5s ease-in-out;">
        <div class="card-header">
            <h2 class="white-text text-center">Minha biblioteca</h2>
        </div>
        <div class="card-body" style="max-height:600px; overflow-y:auto">
        <div class="row card-deck">
            <?php 
            if(isset($dadosJogosBiblioteca) && !empty($dadosJogosBiblioteca)){
            
                foreach($dadosJogosBiblioteca as $dadosJogos){
                    $imagem = isset($dadosJogos->imagem) ? $dadosJogos->imagem : 'placeholder_item.jpg';

                    $img = "{$link}/arquivos/{$imagem}";
            ?>

            <div class="card-item col-12 <?php echo $tamanho = (count($dadosJogosBiblioteca) <= 3) ? "col-md-12 col-lg-6" : "col-md-4 col-lg-3" ?> text-center"> 
                <div class="card">
                    <img src="<?= $img ?>" class="cardImg">
                    <div class="card-content">
                        <p class="white-text">
                            <?= $dadosJogos->titulo ?>
                        </p>
                        <p class="white-text">
                            Adquirido em:
                            <?= $dadosJogos->dt ?>
                        </p>
                        <?php
                            if(isset($dadosJogos->link)){
                                ?>
                                <p class="card-button-wrapper">
                                    <a href='<?= $dadosJogos->link ?>'
                                    class="styledBtn">
                                        Baixar Jogo
                                    </a>
                                </p>
                                <?php
                            } else{
                                ?>
                                    <p class="card-button-wrapper">
                                        <a
                                        class="styledBtn">
                                            Jogo Indisponivel para download.
                                        </a>
                                    </p>
                                <?php
                            }
                        ?>
                        
                    </div>
                </div>
            </div>
            <?php
                }
            }else{
            ?>
                <h3 class="white-text text-center">
                    Você ainda não tem jogos na sua biblioteca! 😥
                </h3>
            <?php
            }
            ?>
        </div>