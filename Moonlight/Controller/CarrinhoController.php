<?php
    namespace Moonlight\Controller;

    use MercadoPago\MercadoPagoConfig;
    use MercadoPago\Client\Preference\PreferenceClient; // 👈 NOVO: O cliente que interage com a API
    use MercadoPago\Resources\Preference\Payer as MPPayer;   // Alias para a classe de dados (Payload)

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

                // o token tem que ser pego no mercado pago, passei ele pro .env
                // no repositorio vai estar apenas o .env.example, pega o arquivo e deixe ele sem o ".example" no nome e insira o seu token lá.
                $token = $_ENV['MERCADOPAGO_ACCESS_TOKEN'] ?? 'APP_USR-6033108192222642-112402-0f3bfaf0b51b22625c79b1e9d115b873-3008632819';
                
                MercadoPagoConfig::setAccessToken($token);

                // Instancia o Client que fará a chamada à API
                $client = new PreferenceClient();

                // Instancia o objeto Payer (Pagador)
                $payer = new MPPayer();


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

                //  EXPLICAÇÃO IMPORTANTE:
                // Vá para o arquivo ngrok.txt na pasta aprendizado para obter mais detalhes sobre este link.
                $url_publica_ipn = "https://phlogistic-maison-sloshily.ngrok-free.dev";

                // arquivo de notificacao para o mercado pago enviar o status de pagamento e atualizarmos no pedido.
                $caminho_notificacao = "/Moonlight/Moonlight/Public/meli/notificacao.php";

                // base_url_retorno basicamente é a parte onde o mercado pago leva o usuario após a compra. (preferi deixar levar pro link acima, pois o nosso site não é publicado (MP so aceita sites com https).)
                $base_url_retorno = $url_publica_ipn . "/Moonlight/Moonlight/Public";

                //usar em produção
                // $payer->name = $_SESSION["Logado_Na_Sessão"]["nm_user"];
                // $payer->email = $_SESSION["Logado_Na_Sessão"]["email"];

                // para testes EM COMPRAS DE CARTÃO:

                //credenciais de teste:

                //Cartão	    Número	                Código de segurança	    Data de vencimento
                // Mastercard    // 5031 4332 1540 6351   // 123                   // 11/30

                //Pra ser aprovado escreva no nome do titular do cartão: APRO 
                //CPF: 12345678909
                //mais detalhes em: https://www.mercadopago.com.br/developers/panel/app

                //USE esse email, pois se não, seu teste irá falhar.
                                
                $payer->name = $_SESSION["Logado_Na_Sessão"]["nm_user"];
                $payer->email = "TESTUSER8052695651117258427@testuser.com";

                $external_reference = uniqid('order_'); // Gera um ID único, como "order_656edadae2e98" | usaremos ele pra poder atualizar status no banco.

                $preferenceData = [
                    "payer" => [
                        "name" => $payer->name,
                        "email" => $payer->email
                    ],
                    "items" => $itens,
                    "external_reference" => $external_reference,
                    "back_urls" => [
                        "success" => "{$base_url_retorno}/compra/sucesso",
                        "failure" => "{$base_url_retorno}/compra/falha",
                        "pending" => "{$base_url_retorno}/compra/pendente"
                    ],
                    "notification_url" => "{$url_publica_ipn}{$caminho_notificacao}",
                    "auto_return" => "approved"
                ];

                try {
                    $preference_criada = $client->create($preferenceData);

                    $preference_id = $preference_criada->id; // vamos salvar no banco

                    // Verificação de segurança:
                    if (empty($preference_id)) {
                        // Isso deve ser raro, mas pode acontecer se a API retornar sucesso sem ID (muito incomum).
                        throw new \Exception("A preferência foi salva, mas o ID retornado está vazio.");
                    }

                    $dataHoraAtual = date('Y-m-d H:i:s');

                    $this->carrinho->salvarPedido($dataHoraAtual, $totalGeral, "pendente", $preference_id, $external_reference); 

                    require "../Views/carrinho/checkout.php";

                } catch (Throwable $e) {
                    // AQUI ESTÁ O ERRO!
                    // Você pode logar o erro:
                    $errorMessage = "Erro ao salvar a preferência no Mercado Pago: " . $e->getMessage();
                    
                    Logger::logError(new \RuntimeException($errorMessage), "MP_PURCHASE_ERROR");

                    // E exibir uma mensagem amigável ao usuário:
                    $_SESSION['modalTitle'] = "Erro de Checkout";
                    $_SESSION['modalMessage'] = "Não foi possível criar a compra no Mercado Pago. Por favor, tente novamente ou verifique as credenciais.";
                    header("Location: " . BASE_URL . "/carrinho");
                    exit;
                }
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