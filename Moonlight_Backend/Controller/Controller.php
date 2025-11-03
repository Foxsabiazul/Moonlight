<?php

namespace Moonlight_Backend\Controller;

abstract class Controller
{

    final protected static function isProtected()
    {
        if(!isset($_SESSION['Logado_Na_Sessão'])){
            $_SESSION['errorTitle'] = "Você não está logado.";
            $_SESSION['error'] = "Faça login para poder acessar o painel de controle.";
            header("Location: /login");
            exit;
        }
    }

}