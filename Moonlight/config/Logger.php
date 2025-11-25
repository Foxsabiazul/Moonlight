<?php

namespace Moonlight\Config;

/**
 * classe pra fazer logs de erros pra um arquivo txt.
 */

class Logger
{
    // caminho
    private static string $logFile = __DIR__ . '/../../logs/app_errors.log';
    private static string $logDebugFile = __DIR__ . '/../../logs/app_debuging.log';

    /**
     * @param \Throwable $e entrega a exceção
     * @param string $context entrega o contexto do erro, se é de banco ou de regra de negocio e etc
     */
    public static function logError(\Throwable $e, string $context = "APP_ERROR"): void
    {
        // Garante que o diretório exista
        if (!is_dir(dirname(self::$logFile))) {
            //pra entender vá até a pasta aprendizado. (mucho texto 🐊);
            mkdir(dirname(self::$logFile), 0777, true);
        }

        $timestamp = (new \DateTime())->format('Y-m-d H:i:s');
        
        $logMessage = "[$timestamp] [$context] ";
        
        // Se for uma exceção de banco, logamos detalhes do SQL
        if ($e instanceof \PDOException) {
            //mensagem de erro do pdo, codigo que o pdo envia o erro
            $logMessage .= "PDO Error: {$e->getMessage()} | Code: {$e->getCode()} ";
        } else {
            // Para outras exceções (como o ModalMessage, ou exceções gerais) // mensagem de erro, arquivo de onde aconteceu, linha do erro.
            $logMessage .= "Exception: {$e->getMessage()} | File: {$e->getFile()} | Line: {$e->getLine()}";
        }

        // pular linha
        $logMessage .= "\n";

        //pra entender vá até a pasta aprendizado. (mucho texto🐊);
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
        
        error_log($logMessage);
    }

    public static function log(string $texto = "mensagem para printar!"){

        // Garante que o diretório exista
        if (!is_dir(dirname(self::$logDebugFile))) {
            //pra entender vá até a pasta aprendizado. (mucho texto 🐊);
            mkdir(dirname(self::$logDebugFile), 0777, true);
        }

        $timestamp = (new \DateTime())->format('Y-m-d H:i:s');

        $logMessage = "[$timestamp] [$texto]";

        $logMessage .= "\n";

        file_put_contents(self::$logDebugFile, $logMessage, FILE_APPEND);

        error_log($logMessage);
    }

}