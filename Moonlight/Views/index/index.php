<div class="container">

    <?php
    if(isset($_SESSION['Logado_Na_Sessão'])){
        ?>
        <div class="card">
            <div class="col-12">
                <?php
                    date_default_timezone_set("America/Sao_Paulo");
                    $hour = date('H');
                    $greeting = "Olá";

                    // Define a saudação com base na hora do dia
                    if ($hour >= 5 && $hour < 12) {
                        $greeting = "manhã";
                    } else if ($hour >= 12 && $hour < 18) {
                        $greeting = "tarde";
                    } else {
                        $greeting = "noite";
                    }

                    $userName = isset($_SESSION['Logado_Na_Sessão']) ? htmlspecialchars($_SESSION['Logado_Na_Sessão']["nm_user"]) : "Usuário";
                    echo "<h3 class='white-text text-center p-x1'>Seja bem vindo " . $userName . ", como vai nessa " . $greeting . "?</h3>";
                ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="card">
        <div class="card-header">
            <h2 class="white-text text-center">Jogos em destaque:</h2>
        </div>
        <div class="card-body">
            <div class="row card-deck">
            <?php 
                //carregar o conteudo dos Jogos da API
                $url = "{$link}/api/jogos.php";
                $dadosJogos = file_get_contents($url);
                $dadosJogos = json_decode($dadosJogos);

                foreach($dadosJogos as $dados) {

                    $imagem = isset($dados->imagem) ? $dados->imagem : 'placeholder_item.jpg';

                    $img = "{$link}/arquivos/{$imagem}";

                    ?>
                    <div class="card-item col-12 col-md-4 col-lg-3 text-center">   
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
                                    <a href="<?= BASE_URL?>/games/index/<?= $dados->id_games ?>"
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