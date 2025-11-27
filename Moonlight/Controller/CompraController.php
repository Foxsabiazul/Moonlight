<?php
    namespace Moonlight\Controller;

    class CompraController extends Controller{

        //apenas um controller pra dar mensagens amigaveis ao usuario após o checkout.

        private function handleNgrokRedirect(string $route) {
            // Obtém o host atual da requisição. Ex: 'meudominio.ngrok-free.app' ou 'localhost/Moonlight'.
            $currentHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
            
            // Verifica se o host atual NÃO é um host local ('localhost' ou '127.0.0.1').
            // Se for um endereço externo (como ngrok), o redirecionamento é necessário para levar o usuário de volta
            // ao ambiente local correto para que o CSS/JS funcione.
            $isLocalhost = (strpos($currentHost, 'localhost') !== false || strpos($currentHost, '127.0.0.1') !== false);
            
            if (!$isLocalhost) {
                $targetUrl = "http://localhost/Moonlight/Moonlight/Public/compra/{$route}";
                
                // Redireciona via JavaScript e, crucialmente, encerra a execução do PHP (exit)
                // para garantir que apenas o script de redirecionamento seja enviado ao navegador,
                // parando o loop.
                echo '<script>location.href="' . $targetUrl . '"</script>';
                exit; 
            }
        }

        public function sucesso() {
            $this->handleNgrokRedirect('sucesso');
            $msgTitle = "🥳 Compra Aprovada com Sucesso!";
            $msgParagraph = "Seus jogos já estão disponíveis na sua biblioteca!";
            require "../Views/compra/index.php";
        }

        public function falha() {
            $this->handleNgrokRedirect('falha');
            $msgTitle = "❌ Pagamento Recusado.";
            $msgParagraph = "Seu pagamento foi recusado pela operadora. Por favor, tente com outra forma de pagamento ou entre em contato com seu banco.";
            require "../Views/compra/index.php";
        }

        public function pendente(){
            $this->handleNgrokRedirect('pendente');
            $msgTitle = "⏳ Pagamento em Análise.";
            $msgParagraph = "Sua compra foi registrada, mas o pagamento (geralmente via Boleto ou Pix) ainda está sendo processado. Atualizaremos o status em 'Meus Pedidos' assim que for confirmado.";
            require "../Views/compra/index.php";
        }
    }