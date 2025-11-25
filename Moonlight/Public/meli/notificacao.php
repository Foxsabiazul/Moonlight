<?php

// Este arquivo é um endpoint simples.

require '../../vendor/autoload.php';
require '../../config/Conexao.php';
require '../../Model/PedidosModel.php'; 
require '../../config/Logger.php'; 

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient; // 👈 Adicionar o Cliente
use MercadoPago\Exceptions\MPApiException;
use Moonlight\Model\PedidosModel;
use Moonlight\config\Conexao;
use Moonlight\config\Logger;

// ⚠️ ATENÇÃO: Confirme se as variáveis de ambiente estão sendo carregadas! 
// Se não usar .env, use a chave diretamente aqui: MercadoPagoConfig::setAccessToken("YOUR_ACCESS_TOKEN");
$token = $_ENV['MERCADOPAGO_ACCESS_TOKEN'] ?? 'APP_USR-6033108192222642-112402-0f3bfaf0b51b22625c79b1e9d115b873-3008632819';
error_log("MERCADOPAGO TOKEN: " . $token); // Deve mostrar o token real, nunca vazio!

if (!$token) {
    Logger::logError(new \Exception("Mercado Pago Access Token ausente"), "CONFIG_ERROR");
    http_response_code(500);
    exit;
}

MercadoPagoConfig::setAccessToken($token); // 👈 Use setAccessToken

$pdo = Conexao::connect();
$pedidosModel = new PedidosModel($pdo);

$get_params = json_encode($_GET);
$texto = 'IPN Recebido (GET): ' . $get_params . 'IPN_RECEBIDO';
Logger::log($texto);

$id = $_GET['id'] ?? $_GET['data_id'] ?? null;
$topic = $_GET['topic'] ?? $_GET['type'] ?? null;

// Validação Inicial
if (empty($id) || empty($topic)) {
    $texto = 'IPN Ignorado: ID ou TOPIC ausentes.' . 'IPN_ERROR';
    Logger::log($texto);
    http_response_code(204); 
    exit;
}

if ($topic !== "payment") {
    Logger::log("IPN Ignorado: Tópico não é 'payment': $topic");
    http_response_code(204);
    exit;
}

if (!$id || !$topic || !is_numeric($id)) {
    Logger::log("IPN Ignorado: ID (value={$id}) ou TOPIC (value={$topic}) ausentes ou inválidos.");
    http_response_code(204);
    exit;
}
$id = (int)$id;

try {
    // 1. Instancia o cliente de pagamento (que já usa o token configurado)
    $paymentClient = new PaymentClient();

    // 2. Busca o objeto de pagamento
    $payment = $paymentClient->get($id); 

    // O SDK moderno retorna um objeto, verificamos se ele tem o ID.
    if ($payment && $payment->id) { 
        // Acessamos as propriedades do OBJETO (não mais array)
        $payment_id = $payment->id;
        $external_reference = $payment->external_reference;
        if (empty($external_reference)) {
            Logger::logError(new \Exception("Preference ID não encontrado no pagamento"), "IPN_HANDLING_ERROR");
            http_response_code(400);
            exit;
        }
        $status = $payment->status; // 'approved', 'pending', etc.
        
        $texto = "Consulta MP SUCESSO: Payment ID: {$payment_id}, Status: {$status}, external_reference: {$external_reference} " . 'MP_CONSULTA_SUCESSO';
        Logger::log($texto);

        $novoStatus = match ($status) {
            'approved' => 'aprovado',
            'pending' => 'pendente',
            'rejected', 'cancelled' => 'cancelado',
            'refunded' => 'reembolsado',
            default => 'pendente',
        };

        // Chame a função da sua PedidosModel!
        $atualizado = $pedidosModel->atualizarStatusPedidoPorPreferenceID($external_reference, $novoStatus);

        if ($atualizado) {
            $texto = "Pedido atualizado com sucesso. Status: {$novoStatus} " . 'BD_SUCESSO';
            Logger::log($texto);
            http_response_code(200);
        } else {
            $texto = "Falha ao atualizar o BD para Preference ID: {$preference_id}. Status: {$novoStatus} " . 'BD_FALHA';
            Logger::log($texto);
            http_response_code(400); 
        }

    } else {
        // Logar que não conseguiu obter a informação do pagamento
        $errorMessage = "Falha ao buscar dados do pagamento ID: {$id} na URL: {$url_resource}. Response: Sem objeto de Payment.";
        Logger::logError(new \RuntimeException($errorMessage), "MP_API_ERROR");
        http_response_code(400);
    }
}catch(MPApiException $e){

    $statusCode = $e->getApiResponse()->getStatusCode();
    $apiError = json_encode($e->getApiResponse()->getContent());
    Logger::logError($e, "MP_API_ERROR STATUS: $statusCode, RESPONSE: $apiError");
    http_response_code(500);

} catch (\Throwable $e) {
    // Logar qualquer erro inesperado
    http_response_code(500); 
    Logger::logError($e, "IPN_HANDLING_ERROR");
    error_log($e->getMessage() . " " . $e->getTraceAsString());
}   