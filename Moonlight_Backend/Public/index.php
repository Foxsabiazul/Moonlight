<?php

use Moonlight_Backend\config\Sanitizador;
use Moonlight_Backend\Controller\IndexController;
use Moonlight_Backend\Controller\UsuarioController;
use Moonlight_Backend\Controller\CategoriaController;

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
    <title>Painel de Controle - Moonlight</title>
    <base href="http://<?= $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"] ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet';">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"></noscript>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/index/style.css">
    <link rel="stylesheet" href="css/index/login.css">
    <link rel="stylesheet" href="css/index/erro.css">
<?php
    $cssMap = [
        "store" => [
            "css/storePage/store.css"
        ],
        "product" => [
            "css/productPage/product.css"
        ],
        "FAQ" => [
            "css/faqPage/faq.css"
        ],
        "news" => [
            "css/newsPage/news.css"
        ],
        "cart" => [
            "css/cartPage/cart.css"
        ],
        "erro404" => [
            "css/index/erro404.css"
        ]
    ];

    if (isset($cssMap[$controller])) {
        foreach ($cssMap[$controller] as $cssFile) {
            if (file_exists($cssFile)) {
                echo "<link rel='stylesheet' href=\"$cssFile\">";
            }
        }
    }
    ?>

    <?php
    $jsMap = [
        "contact" => ["assets/js/contact/contact.js"],
        "store" => ["assets/js/store/storeFilter.js"],
        "product" => [
            "assets/js/product/product.js",
            "assets/js/product/cart.js"
        ],
        "cart" => [
            "assets/js/cart/loadcart.js"
        ],
    ];

    if (isset($jsMap[$controller])) {
        foreach ($jsMap[$controller] as $jsFile) {
            if (file_exists($jsFile)) {
                echo "<script src=\"$jsFile\"></script>";
            }
        }
    }
    ?>
    <script src="js/index/login.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/jquery.inputmask.min.js"></script>
    <script src="js/bindings/inputmask.binding.js"></script>
    <script src="js/parsley.min.js"></script>
    <script src="js/index/layout.js"></script>
    <script>

        function showMenu() {
            let bars = document.getElementById('fa-bars');
            bars.classList.toggle("active");

            let menu = document.querySelector('.header-nav');
            menu.classList.toggle("active");
        }

        function showDropdown() {
            let dropdown = document.querySelector('.dropdown-container');
            dropdown.classList.toggle("active");
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
    <?php 
    
    /**
     * 3 cenários possíveis:
     * 1) se marmanjo não estiver logado e n tiver enviado o form, mostra a tela de login
     * 2) se marmanjo não estiver logado e tiver enviado o form, processa o login
     * 3) se marmanjo estiver logado, mostra o painel de controle
     */
    if(!isset($_SESSION['Logado_Na_Sessão']) && (!$_POST)) {
        include "../Views/index/Login.php";

    } else if(!isset($_SESSION['Logado_Na_Sessão']) && ($_POST)){

        /**
         * classe que serve apenas para usar trim() no que for mandado à ele.
        */
        $email = Sanitizador::sanitizar($_POST['email']);
        $senha = Sanitizador::sanitizar($_POST['senha']);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            /**
             * modalTitle e Message servem para dar mensagens personalizadas ao usuario com um Modal.
             * nao se preocupe com a ativação do modal, ele é ativado sozinho quando chega uma mensagem à ele.
             * ele está em Views/Components/FlashMessage.php
             */
            $_SESSION['modalTitle'] = "E-mail Inválido.";
            $_SESSION['modalMessage'] = "O Email Inserido é inválido. Tente Novamente com um válido.";
            echo "<script>location.href='index';</script>";
            exit;
        } else if (empty($senha)) {
            $_SESSION['modalTitle'] = "Digite a senha.";
            $_SESSION['modalMessage'] = "A senha não está preenchida no formulário. Digite a senha.";
            echo "<script>location.href='index';</script>";
            exit;
        } 

        $acao = new IndexController();
        $acao->verificar($email, $senha);

    } else {
        
        ?>

        <header class="header">
            <div class="container">
                <div class="flex">
                    <div class="flex-col1">
                        <a href="index" class="headerLogo" title="Pagina Inicial">
                            <img src="img/index/MoonlightMenor.png" alt="logo Moonlight">
                        </a>
                    </div>
                    <div class="flex-col2">
                        <a href="javascript:showMenu()" class="header-menu" id="header-menu">
                            <i class="fas fa-bars" id="fa-bars"></i>
                        </a>
                        <nav class="header-nav" id="header-nav">
                            <ul class="nav-ul">
                                <li class="nav-li">
                                    <a class="nav-btn" title="Listagem de Categorias" href="Categoria">Categorias</a>
                                </li>
                                <li class="nav-li">
                                    <a class="nav-btn" title="Listagem de Games" href="Games">Games</a>
                                </li>
                                <li class="nav-li">
                                    <a class="nav-btn" title="Listagem de Favoritos" href="Favoritos">Favoritos</a>
                                </li>
                                <li class="nav-li">
                                    <div class="dropdown-container">
                                        <a href="javascript:showDropdown()" class="user-menu" id="user-menu" title="Menu do usuario">
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
                                            <span class="dropdown-arrow">&#9660</span> 
                                            <!-- esse trecho no span é referente a um caracter de seta apontando pra baixo, usado assim para não precisar de uma imagem para representar essa seta. -->

                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="index/logout" title="Sair" id="lastBtn"><i class="fas fa-power-off"></i> Sair</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
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
                $controller = ucfirst($controller)."Controller";
                
                $page = "../Controller/{$controller}.php";

                if (file_exists($page)) {

                    // como estamos usando o composer ele não vai funcionar aqui
                    // se você decidir usar a variavel $controller do jeito que estava iria quebrar, 
                    // por causa do 'use' lá em cima, ele pensa que vamos pegar o namespace pra chamar a classe.

                    $fullClassName = "Moonlight_Backend\\Controller\\{$controller}";

                    $control = new $fullClassName();
                    $control->$acao($id);

                } else include "../Views/index/erro.php";
                ?>
        </main>

        <footer class="footer">
            <div class="footerClass">
                <p>
                    <a href="index" title="Pagina Inicial">
                        <img src="img/index/Moonlight.png" alt="logo Moonlight">
                    </a>
                </p>
                
                <p class="description">
                    © Moonlight - 2025
                </p>
            </div>
        </footer>

        <?php
    }

    ?>
</body>
</html>
