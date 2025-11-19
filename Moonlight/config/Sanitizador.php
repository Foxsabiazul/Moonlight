<?php

namespace Moonlight\Config;

class Sanitizador
{
    /**
     * Tudo o que chegar de item para esta classe em String
     * será limpado os espaços vazios, sanitizando e daí devolvendo o item de volta pra classe que enviou.
     */
    public static function sanitizar(String $itemASerSanitizado):String
    {
       return trim(htmlspecialchars($itemASerSanitizado));
    }
}