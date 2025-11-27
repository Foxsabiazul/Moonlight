<?php
    $filtroselectionado = $_GET['filtro'] ?? '';
    $showOperador = ($filtroselectionado == 'preco' || $filtroselectionado == 'data_lancamento');

    if($filtroselectionado == 'preco' || $filtroselectionado == 'data_lancamento'){
        if($filtroselectionado == 'preco'){
            $inputType = 'number';
        } else{
            $inputType = 'date';
        }
    } else{
        $inputType = 'text';
    }
?>

<div class="container">

    <div class="card">
        <div class="card-header jc-between">
            <div class="queue">
                <h2 class="white-text text-left">
                    Resultados em relação ao termo:
                </h2>
                <p class="white-text">
                    <?= htmlspecialchars($tituloBusca) ?>
                </p>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>/search" method="GET" class="formSearch as-center">
                <div class="input-search-group">
                    <input class="inputStyleGroup" type="<?= $inputType ?>" name="termo" placeholder="Buscar jogos, preços e etc com base nos filtros..." 
                    value="<?= htmlspecialchars($_GET['termo'] ?? '') ?>" id="termoInput">
                    <button type="submit" class="btnStyleGroup searchBtn"><i class="fas fa-search"></i></button>
                </div>

                <div class="select-group">
                    <div class="group">
                        <label style="text-align: left;" class="formLabel" for="order">Ordenar por:</label>
                        <select name="order" id="order" class="selectForm-control">
                            <option value="">Ordem padrão <span>▼</span></option>
                            <option value="titulo" <?= (($_GET['order'] ?? '') == 'titulo') ? 'selected' : '' ?>>Nome do jogo</option>
                            <option value="preco" <?= (($_GET['order'] ?? '') == 'preco') ? 'selected' : '' ?>>Preço</option>
                            <option value="data_lancamento" <?= (($_GET['order'] ?? '') == 'data_lancamento') ? 'selected' : '' ?>>Data de Lançamento</option>
                        </select>
                    </div>
                    <div class="group">
                        <label style="text-align: left;" class="formLabel" for="filtro-select">Filtrar por:</label>
                        <select class="selectForm-control" name="filtro" id="filtro-select">
                            <option value="">Filtrar padrão <span>▼</span></option>
                            <option value="titulo" <?= (($_GET['filtro'] ?? '') == 'titulo') ? 'selected' : '' ?>>Nome do jogo</option>
                            <option value="preco" <?= (($_GET['filtro'] ?? '') == 'preco') ? 'selected' : '' ?>>Preço do jogo</option>
                            <option value="data_lancamento" <?= (($_GET['filtro'] ?? '') == 'data_lancamento') ? 'selected' : '' ?>>Data de lançamento</option>
                        </select>
                    </div>
                    <div class="group">
                        <!-- Este select será exibido apenas para o filtro "Valor" -->
                        <label id="labelOperador" style="text-align: left;" class="formLabel" <?= $showOperador ? '' : 'hidden' ?> <?= $showOperador ? '' : 'disabled' ?> for="valor-operador">Operar por:</label>
                        <select class="selectForm-control"
                            <?= $showOperador ? '' : 'hidden' ?>
                            <?= $showOperador ? '' : 'disabled' ?> 
                        name="operador" id="valor-operador">
                            <option id="operador-igual" <?= (($_GET['operador'] ?? '') == '=') ? 'selected' : '' ?> value="=">Igual a</option>
                            <option id="operador-maiorque" <?= (($_GET['operador'] ?? '') == '>') ? 'selected' : '' ?> value=">">Maior que</option>
                            <option id="operador-menorque" <?= (($_GET['operador'] ?? '') == '<') ? 'selected' : '' ?> value="<">Menor que</option>
                        </select>
                    </div>
                    <div class="group">
                        <label style="text-align: left;" class="formLabel" for="categoria">Filtrar por categoria:</label>
                        <select class="selectForm-control" name="categoria" id="categoria">
                            <option value="">Filtrar por todas as categorias <span>▼</span></option>
                            <?php
                                //pegar as categorias da API
                                $link = "http://localhost/Moonlight/Moonlight_Backend/public/api/categorias.php";
                                $dadosCategoria = file_get_contents($link);
                                $dadosCategoria = json_decode($dadosCategoria);
                                $categoriaSelecionada = $_GET['categoria'] ?? '';

                                foreach ($dadosCategoria as $dados) {
                                    $selected = ($dados->id_categoria == $categoriaSelecionada) ? 'selected' : '';
                                    ?>
                                    <option value="<?=$dados->id_categoria?>" <?= $selected ?> >Filtrar por <?=$dados->nm_cat?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>


            </form>
            <div id="lista-de-jogos" class="row card-deck">
                <?php 
                $totalCarregados = count($dadosJogos);
                $limitePorPagina = 8; // a API usa limite de 8 jogos
                $link = "http://localhost/Moonlight/Moonlight_Backend/public";

                if(!empty($dadosJogos)){
                    // Variável para rastrear se todos os jogos foram carregados
                    
                    ?>
                    <hr class="white-text text-center mt-4">
                    <?php
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
                } else{

                ?>
                <hr class="white-text text-center mt-4">
                <h3 class="white-text text-center mt-4">
                    <?= $resultado ?>
                </h3>
                <?php 
                }
                ?>
                </div>
                
                <div class="text-center mt-4">
                    <button 
                        id="btn-carregar-mais" 
                        class="styledBtn w-50"
                        data-next-page="2"
                        <?php if(isset($termoBusca)): ?>data-search-termo="<?= htmlspecialchars($termoBusca) ?>" <?php endif; ?>
                        <?php if(isset($_GET['order'])): ?>data-order="<?= htmlspecialchars($_GET['order']) ?>" <?php endif; ?>
                        <?php if(isset($_GET['filtro'])): ?>data-filtro="<?= htmlspecialchars($_GET['filtro']) ?>" <?php endif; ?>
                        <?php if(isset($_GET['operador'])): ?>data-operador="<?= htmlspecialchars($_GET['operador']) ?>" <?php endif; ?>
                        <?php if(isset($_GET['categoria'])): ?>data-categoria="<?= htmlspecialchars($_GET['categoria']) ?>" <?php endif; ?>
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