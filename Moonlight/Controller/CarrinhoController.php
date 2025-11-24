<?php
    namespace Moonlight\Controller;

    use MercadoPago\Payer;
    use MercadoPago\Preference;
    use MercadoPago\SDK;
    use Moonlight\config\Conexao;
    use Moonlight\config\Logger;
    use Moonlight\config\ModalMessage;
    use Moonlight\Model\CarrinhoModel;
    use PDO;
    use Throwable;

    class CarrinhoController extends Controller {

        private $carrinho;

        public function __construct(){
            // para fazer conexão singleton
            $pdo = Conexao::connect();
            $this->carrinho = new CarrinhoModel($pdo);
        }

        public function index() {
            require "../Views/carrinho/index.php";
        }

        public function adicionar($id, $link) {
            $url = "{$link}/api/jogo.php?id={$id}";
            $dadosJSON = file_get_contents($url);
            $dados = json_decode($dadosJSON);

            if (empty($dados->id_games)) {
                $_SESSION['modalTitle'] = "Jogo inválido";
                $_SESSION['modalMessage'] = "O Jogo não foi encontrado.";
                header("Location: " . BASE_URL . "/carrinho");
                exit;
            }

            $_SESSION["carrinho"][$id] = array(
                "id_games" => $id,
                "titulo" => $dados->titulo,
                "preco" => $dados->preco,
                "imagem" => $dados->imagem
            );

            // no caso de clicar no comprar agora.
            $redirecionarParaCarrinho = $_GET['redirect'] ?? null;

            if($redirecionarParaCarrinho === 'carrinho'){
                header("Location: " . BASE_URL . "/carrinho");
                exit;
            }

            // se foi no adicionar ao carrinho
            header("Location: " . BASE_URL . "/games/" . $id);
            exit;
        }

        public function excluir($id) {
            //retirar um item do carrinho

            unset($_SESSION["carrinho"][$id]);

            $redirecionarParaDetalhes = $_GET['redirect'] ?? null;

            if($redirecionarParaDetalhes === 'detalhes'){
                header("Location: " . BASE_URL . "/games/" . $id);
                exit;
            }

            header("Location: " . BASE_URL . "/carrinho");
            exit;
        }

        public function limpar() {
            unset($_SESSION["carrinho"]);
            header("Location: " . BASE_URL . "/carrinho");
            exit;
        }

        public function checkout() {
            if (isset($_SESSION["Logado_Na_Sessão"]["id_user"]) && !empty($_SESSION["carrinho"])) {
                //é pq esta logado e carrinho com itens

                if (!class_exists('\MercadoPago\SDK')) {
                    // Note: Este require deve estar no index.php, não no Controller,
                    // mas o SDK precisa ser configurado aqui.
                    require 'vendor/autoload.php'; 
                }

                $token = $_ENV['MERCADOPAGO_ACCESS_TOKEN'] ?? '';
                \MercadoPago\SDK::setAccessToken($token);

                $preference = new Preference();

                $payer = new Payer();
                $payer->name = $_SESSION["Logado_Na_Sessão"]["nm_user"];
                $payer->email = $_SESSION["Logado_Na_Sessão"]["email"];

                $preference->payer = $payer;

                $itens = [];

                $totalGeral = 0;

                foreach($_SESSION["carrinho"] as $jogos){
                    $itens[] = array(
                        "title" => $jogos["titulo"],
                        "quantity" => 1,
                        "currency_id" => "BRL",
                        "unit_price" => (float)$jogos["preco"]
                    );

                    $precoItem = (float)$jogos['preco'];
                    $totalGeral += $precoItem;
                }

                $preference->items = $itens;

                $preference->back_urls = array(
                    "success" => "https://www.seusite.com.br/meli/sucesso.php",
                    "failure" => "https://www.seusite.com.br/meli/falha.php",
                    "pending" => "https://www.seusite.com.br/meli/pendente.php"
                );

                $preference->notification_url = "https://www.seusite.com.br/meli/notificacao.php";

                $preference->auto_return = "approved";

                $preference->save();

                $preference_id = $preference->id;

                $dataHoraAtual = date('Y-m-d H:i:s');

                $this->carrinho->salvarPedido($dataHoraAtual, $totalGeral, "iniciado", $preference_id);

                require "../Views/carrinho/checkout.php";
            } else if(isset($_SESSION["Logado_Na_Sessão"]["id_user"]) && empty($_SESSION["carrinho"])){
                $_SESSION['modalTitle'] = "Seu carrinho está vazio!";
                $_SESSION['modalMessage'] = "Não é possivel realizar checkout com carrinho vazio.";
                header("Location: " . BASE_URL . "/carrinho");
                exit;
            } else{
                //não está logado
                $_SESSION['modalTitle'] = "Você não está logado";
                $_SESSION['modalMessage'] = "Faça login primeiro antes de realizar alguma compra.";
                header("Location: " . BASE_URL . "/usuario/access");
                exit;
            }
        }
    }