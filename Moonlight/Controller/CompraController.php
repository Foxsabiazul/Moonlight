<?php
    namespace Moonlight\Controller;

    class CompraController extends Controller{

        //apenas um controller pra dar mensagens amigaveis ao usuario após o checkout.

        public function sucesso() {
            $msgTitle = "🥳 Compra Aprovada com Sucesso!";
            $msgParagraph = "Seus jogos já estão disponíveis na sua biblioteca!";
            require "../Views/compra/index.php";
        }

        public function falha() {
            $msgTitle = "❌ Pagamento Recusado.";
            $msgParagraph = "Seu pagamento foi recusado pela operadora. Por favor, tente com outra forma de pagamento ou entre em contato com seu banco.";
            require "../Views/compra/index.php";
        }

        public function pendente(){
            $msgTitle = "⏳ Pagamento em Análise.";
            $msgParagraph = "Sua compra foi registrada, mas o pagamento (geralmente via Boleto ou Pix) ainda está sendo processado. Atualizaremos o status em 'Meus Pedidos' assim que for confirmado.";
            require "../Views/compra/index.php";
        }
    }