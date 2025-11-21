<?php

ob_start();

// ----------------------------------------------------
// BASE_URL
// ----------------------------------------------------

// Obtém o protocolo (http ou https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

// Obtém o nome do host (ex: localhost, meudominio.com)
$host = $_SERVER['HTTP_HOST'];

// Obtém o caminho do script atual (ex: /Moonlight/Moonlight/Public/index.php)
// usando mod_rewrite as requisições sempre passam pelo index.php,
// então $_SERVER['SCRIPT_NAME'] será o caminho até o index.php.

$script_name = $_SERVER['SCRIPT_NAME'];

// Remove o nome do arquivo 'index.php' e a pasta 'Public' do caminho do script
// O $base_dir será o caminho do projeto no servidor (ex: /Moonlight/Moonlight/Public/)
// Usamos dirname() duas vezes para subir de 'index.php' para 'Public' e depois subir de 'Public' para a raiz do projeto.

// Encontra a raiz base onde o index.php está
$path_parts = pathinfo($script_name);
$root_dir = $path_parts['dirname'];

// Garante que o caminho base termine SEM a barra.
// Exemplo: /Moonlight/Moonlight_Backend/Public
$base_path = rtrim($root_dir, '/'); 

// Constrói a URL completa e define a constante sem a barra final
define('BASE_URL', "{$protocol}://{$host}{$base_path}");

require __DIR__ .  "/../vendor/autoload.php";
session_start();
$controller = $_GET["param"] ?? "index";

if(empty($controller)){
    $controller = "index";
}

    
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moonlight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet';">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"></noscript>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/forms.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/erro.css">
<?php
    $cssMap = [
        "store" => [
            "/css/storePage/store.css"
        ],
        "product" => [
            "/css/productPage/product.css"
        ],
        "FAQ" => [
            "/css/faqPage/faq.css"
        ],
        "news" => [
            "/css/newsPage/news.css"
        ],
        "cart" => [
            "/css/cartPage/cart.css"
        ],
        "erro404" => [
            "/css/index/erro404.css"
        ]
    ];

    if (isset($cssMap[$controller])) {
        foreach ($cssMap[$controller] as $cssFile) {
            if (file_exists($cssFile)) {
                echo "<link rel='stylesheet' href=" . BASE_URL . "\"$cssFile\">";
            }
        }
    }
    ?>
    <?php
    $jsMap = [
        "contact" => ["/assets/js/contact/contact.js"],
        "store" => ["/assets/js/store/storeFilter.js"],
        "product" => [
            "/assets/js/product/product.js",
            "/assets/js/product/cart.js"
        ],
        "cart" => [
            "/assets/js/cart/loadcart.js"
        ],
    ];

    if (isset($jsMap[$controller])) {
        foreach ($jsMap[$controller] as $jsFile) {
            if (file_exists($jsFile)) {
                echo "<script src=" . BASE_URL . "\"$jsFile\"></script>";
            }
        }
    }
    ?>
    <script src="<?= BASE_URL ?>/js/jquery-3.5.1.min.js"></script>
    <script src="<?= BASE_URL ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/js/parsley.min.js"></script>
    <script>

        function fecharModal(){
            const modalcontainer = document.getElementById('modal-container');
            const modaloverlay = document.getElementById('modal-overlay');
            modalcontainer.style.display = "none";
            modalcontainer.style.opacity = "0";
            modaloverlay.style.display = "none";
            modaloverlay.style.opacity = "0";
        }

        window.onload = () =>{
            const modalcontainer = document.getElementById('modal-container');
            const modaloverlay = document.getElementById('modal-overlay');
            if(modalcontainer){
                modalcontainer.style.display = "flex";
                modaloverlay.style.display = "flex";
                modalcontainer.style.opacity = "1";
                modaloverlay.style.opacity = "1";
            }
        }

        // function showDropdown() {
        //     let dropdown = document.querySelector('.dropdown-container');
        //     dropdown.classList.toggle("active");
        // }


        function showMenu() {
            let bars = document.getElementById('fa-bars');
            bars.classList.toggle("active");

            let menu = document.querySelector('.header-nav');
            menu.classList.toggle("active");
        }

        mostrarSenha = function() {
            const campo = document.getElementById('senha');
            if (campo.type === 'password') {
                campo.type = 'text';
            } else {
                campo.type = 'password';
            }
        }

    </script>
</head>
<body>
    <?php
    /**
     * Modal de mensagens flash (sucesso, erro, aviso etc.)
     */
     require '../Views/Components/FlashMessage.php';
     ?>
        <header class="header">
            <div class="container">
                <div class="flex">
                    <div class="flex-col1">
                        <a href="<?= BASE_URL ?>/index" class="headerLogo" title="Pagina Inicial">
                            <img src="<?= BASE_URL ?>/img/index/MoonlightMenor.png" alt="logo Moonlight">
                        </a>
                    </div>
                    <div class="flex-col2">
                        <a href="javascript:showMenu()" class="header-menu" id="header-menu">
                            <i class="fas fa-bars" id="fa-bars"></i>
                        </a>
                        <nav class="header-nav" id="header-nav">
                            <ul class="nav-ul">
                                <li class="nav-li dropdown-center">
                                    <a class="nav-btn" title="Categorias de Jogos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Categorias 
                                        <span class="dropdown-toggle"></span>
                                    </a>
                                    <ul class="dropdown-menu">

                                <?php
                                    //pegar as categorias da API
                                    $link = "http://localhost/Moonlight/Moonlight_Backend/public/api/categorias.php";
                                    $dadosCategoria = file_get_contents($link);
                                    $dadosCategoria = json_decode($dadosCategoria);
                                    foreach ($dadosCategoria as $dados) {
                                        ?>
                                        <li>
                                            <a class="dropdown-item black-text" 
                                            href="<?= BASE_URL ?>/categoria/index/<?=$dados->id_categoria?>">
                                                <?=$dados->nm_cat?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                ?>
                                    </ul>
                                </li>
                                <?php
                                    if(isset($_SESSION['Logado_Na_Sessão'])){
                                        ?>
                                            <li class="nav-li dropdown-center">
                                                <a class="nav-btn-user-menu" id="user-menu" title="Menu do usuario" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?php
                                                        // Obtém a hora atual para definir a saudação
                                                        date_default_timezone_set("America/Sao_Paulo");
                                                        $hour = date('H');
                                                        $greeting = "Olá";

                                                        // Define a saudação com base na hora do dia
                                                        if ($hour >= 5 && $hour < 12) {
                                                            $greeting = "Bom dia";
                                                        } else if ($hour >= 12 && $hour < 18) {
                                                            $greeting = "Boa tarde";
                                                        } else {
                                                            $greeting = "Boa noite";
                                                        }
                                                        
                                                        $userName = isset($_SESSION['Logado_Na_Sessão']) ? htmlspecialchars($_SESSION['Logado_Na_Sessão']["nm_user"]) : "Usuário";

                                                        // Exibe a saudação e o nome do usuário
                                                        echo $greeting . ", " . $userName . "!";
                                                    ?>
                                                    <span class="dropdown-toggle"></span> 
                                                    <!-- esse trecho no span é referente a um caracter de seta apontando pra baixo, usado assim para não precisar de uma imagem para representar essa seta. -->

                                                </a>
                                                <ul class="dropdown-menu">
                                                    <?php if($_SESSION['Logado_Na_Sessão']['tipo'] == 'admin'): ?>
                                                    <li>
                                                        <a class="dropdown-item black-text" href="http://localhost/Moonlight/Moonlight_Backend/Public/index">Entrar no administrativo</a>
                                                    </li>

                                                    <?php endif; ?>
                                                    <li>
                                                        <a class="dropdown-item black-text" href="<?= BASE_URL ?>/usuario/logout" title="Sair" id="lastBtn"><i class="fas fa-power-off"></i> Sair</a>
                                                    </li>

                                                </ul>
                                            </li>
                                        <?php
                                    } else{
                                        ?>
                                        <li class="nav-li">
                                            <a href="<?= BASE_URL ?>/usuario/access" class="nav-btn-user-menu" title="Entrar">Fazer login</a>
                                        </li>
                                        <?php
                                    }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <main>
                <?php
                $param = explode("/", $controller);

                $controller = $param[0] ?? "index";
                $acao = $param[1] ?? "index";
                $id = $param[2] ?? NULL;

                // Verifica se a $acao é um número e se o $id ainda não foi definido.
                // Isso cobre o formato: /controller/12 (onde 12 é o ID)
                if (is_numeric($acao) && $id === NULL) {
                    // Se for um número, move o valor de $acao para $id
                    $id = $acao;
                    // E define a $acao padrão como "index"
                    $acao = "index";
                } 
                // Trata o caso padrão onde a Ação está vazia (Ex: /controller)
                else if(empty($acao)){
                    $acao = "index";
                }

                $controller = ucfirst($controller)."Controller";
                
                $page = "../Controller/{$controller}.php";

                if (file_exists($page)) {

                    // como estamos usando o composer ele não vai funcionar aqui
                    // se você decidir usar a variavel $controller do jeito que estava iria quebrar, 
                    // por causa do 'use' lá em cima, ele pensa que vamos pegar o namespace pra chamar a classe.

                    $fullClassName = "Moonlight\\Controller\\{$controller}";
                    $link = "http://localhost/Moonlight/Moonlight_Backend/public";

                    $control = new $fullClassName();
                    $control->$acao($id, $link);

                } else include "../Views/index/erro.php";
                ?>
        </main>

        <footer class="footer">
            <div class="footerClass">
                <p>
                    <a href="<?= BASE_URL ?>/index" title="Pagina Inicial">
                        <img src="<?= BASE_URL ?>/img/index/Moonlight.png" alt="logo Moonlight">
                    </a>
                </p>
                
                <p class="description">
                    ©Moonlight - 2025
                </p>
            </div>
        </footer>

</body>
</html>

<?php

ob_end_flush();

?>
