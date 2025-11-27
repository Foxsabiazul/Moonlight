<?php
    namespace Moonlight\Controller;

    use Moonlight\config\Conexao;
    use Moonlight\Model\PedidosModel;

    class PedidosController extends Controller {

        private $pedidos;

        public function __construct(){
            // para fazer conexão singleton
            $pdo = Conexao::connect();
            $this->pedidos = new PedidosModel($pdo);
        }

        public function index(){
            parent::isProtected();

            $dadosPedidos = $this->pedidos->listarPedidos();
            $dadosPedidos = $dadosPedidos ?? [];


            require "../Views/pedidos/index.php";
        }
    }