<?php

ob_start();

    // ----------------------------------------------------
    // BASE_URL
    // ----------------------------------------------------

    // Obtém o protocolo (http ou https)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

    // Obtém o nome do host (ex: localhost, meudominio.com)
    $host = $_SERVER['HTTP_HOST'];

    // Obtém o caminho do script atual (ex: /Moonlight/Moonlight_Backend/Public/index.php)
    // usando mod_rewrite as requisições sempre passam pelo index.php,
    // então $_SERVER['SCRIPT_NAME'] será o caminho até o index.php.

    $script_name = $_SERVER['SCRIPT_NAME'];

    // Remove o nome do arquivo 'index.php' e a pasta 'Public' do caminho do script
    // O $base_dir será o caminho do projeto no servidor (ex: /Moonlight/Moonlight_Backend/Public/)
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

    use Moonlight_Backend\Controller\UsuarioController;
    
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Moonlight</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet';">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"></noscript>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/forms.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/erro.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/index/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
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
    <!-- <script src="/js/index/login.js"></script> -->
    <!-- <script src="/js/index/layout.js"></script> -->
    <script src="<?= BASE_URL ?>/js/jquery-3.5.1.min.js"></script>
    <script src="<?= BASE_URL ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>/js/jquery.inputmask.min.js"></script>
    <script src="<?= BASE_URL ?>/js/jquery.maskedinput-1.2.1.js"></script>
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

        function fecharModalExclusao(){
            const modalContainer = document.getElementById('modalContainer');
            const modalOverlay = document.getElementById('modalOverlay');
            modalContainer.style.display = "none";
            modalContainer.style.opacity = "0";
            modalOverlay.style.display = "none";
            modalOverlay.style.opacity = "0";
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

        function confirmarExclusao(event, id, tabela){
            const modalContainer = document.getElementById('modalContainer');
            const modalOverlay = document.getElementById('modalOverlay');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            let idParaExcluir = id;

            modalTitle.innerText = "Excluir Registro";
            modalMessage.innerText = "Deseja excluir este registro da tabela de " + tabela + "?";
            modalContainer.style.display = "flex";
            modalOverlay.style.display = "flex";
            modalContainer.style.opacity = "1";
            modalOverlay.style.opacity = "1";
            
            
            // Limpa listeners anteriores para evitar múltiplos eventos
            document.getElementById('btnConfirmar').removeEventListener("click", executeExclusao);
            document.getElementById('btnCancelar').removeEventListener("click", cancelarExclusao);

            function executeExclusao() {
                if(idParaExcluir){
                    // Se o usuário clicar em Confirmar, redireciona para a URL de exclusão
                    location.href = "<?= BASE_URL ?>" + "/" + tabela + "/excluir/" + idParaExcluir;
                }
            }
            
            function cancelarExclusao() {
                idParaExcluir = null;
                fecharModalExclusao();
            }
            
            // Adiciona os listeners
            document.getElementById('btnConfirmar').addEventListener("click", executeExclusao);
            document.getElementById('btnCancelar').addEventListener("click", cancelarExclusao);
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

        $acao = new UsuarioController();
        $acao->login();

    } else {
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
                                <li class="nav-li">
                                    <a class="nav-btn" title="Listagem de Categorias" href="<?= BASE_URL ?>/categoria">Categorias</a>
                                </li>
                                <li class="nav-li">
                                    <a class="nav-btn" title="Listagem de Jogos" href="<?= BASE_URL ?>/games">Jogos</a>
                                </li>
                                <li class="nav-li">
                                    <a class="nav-btn" title="Listagem de Usuarios" href="<?= BASE_URL ?>/usuario">Usuarios</a>
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
                                                <a href="http://localhost/Moonlight/Moonlight/Public/index">Entrar na loja</a>
                                            </li>
                                            <li>
                                                <a href="<?= BASE_URL ?>/usuario/logout" title="Sair" id="lastBtn"><i class="fas fa-power-off"></i> Sair</a>
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
                    <a href="<?= BASE_URL ?>/index" title="Pagina Inicial">
                        <img src="<?= BASE_URL ?>/img/index/Moonlight.png" alt="logo Moonlight">
                    </a>
                </p>
                
                <p class="description">
                    ©Moonlight - 2025
                </p>
            </div>
        </footer>

        <?php
    }

    ?>
</body>
</html>

<?php

ob_end_flush();

?>
