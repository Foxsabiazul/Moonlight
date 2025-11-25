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
                $token = $_ENV['MERCADOPAGO_ACCESS_TOKEN'] ?? '';
                
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
                // Pro esquema de validar status da compra de forma real e ficar bacana pro metabase funcionar,
                // precisamos usar um endereço ip ou dominio publico pro mercado pago acessar,
                // então pra conseguir esta proeza no localhost, precisamos usar o app "ngrok"
                // ele criará um tunel seguro do localhost para um dominio na internet, aí o
                // que o mercado pago mandar pra ele, ngrok envia pra cá.

                // procurem: "ngrok download" e tentem executar ngrok http 80 quando baixarem,
                // aí vc precisa logar e acessar essa url: https://dashboard.ngrok.com/get-started/your-authtoken
                // copie o que está no command line e mande no terminal do ngrok que vc tem no pc, envie,
                // logo em seguida execute o comando ngrok http 80 normalmente, 
                // aí tu passa pra essa variavel aqui essa bomba
                // que está no seu Forwarding:

                // a URL do ngrok para IPN (SUBSTITUA PELA SUA URL ATUAL!)
                // Esta URL precisa ser HTTPS/domínio público para o Mercado Pago enviar a notificação.
                // Lembre-se: substitua pelo endereço que o ngrok te der AGORA no terminal aberto.

                // Instant Payment Notification = IPN. 
                // mecanismo de comunicação seguro e automático 
                // USADO POR GATEWAYS para informar seu servidor
                // sobre uma mudança de status em uma transação
                $url_publica_ipn = "https://phlogistic-maison-sloshily.ngrok-free.dev";
                $caminho_notificacao = "/Moonlight/Moonlight/Public/meli/notificacao.php";

                $base_url_retorno = $url_publica_ipn . "/Moonlight/Moonlight/Public";

                //usar em produção
                // $payer->name = $_SESSION["Logado_Na_Sessão"]["nm_user"];
                // $payer->email = $_SESSION["Logado_Na_Sessão"]["email"];

                //usar pra testes
                $payer->name = $_SESSION["Logado_Na_Sessão"]["nm_user"];
                $payer->email = "TESTUSER8052695651117258427@testuser.com";

                $external_reference = uniqid('order_'); // Gera um ID único, como "order_656edadae2e98"

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

                    // Se chegou aqui, a preferência foi salva com sucesso.
                    $preference_id = $preference_criada->id;

                    // Verificação de segurança:
                    if (empty($preference_id)) {
                        // Isso deve ser raro, mas pode acontecer se a API retornar sucesso sem ID (muito incomum).
                        throw new \Exception("A preferência foi salva, mas o ID retornado está vazio.");
                    }


                    $dataHoraAtual = date('Y-m-d H:i:s');
                    // Mudei o status inicial de volta para "iniciado" (ou "pendente", se preferir)
                    // porque o status "pendente" que você usou estava correto para o salvamento inicial.
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