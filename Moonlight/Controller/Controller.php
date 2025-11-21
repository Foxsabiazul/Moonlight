<?php

namespace Moonlight\Controller;

abstract class Controller
{
    final protected static function isProtected()
    {
        if(!isset($_SESSION['Logado_Na_Sessão'])){
            $_SESSION['modalTitle'] = "Você não está logado.";
            $_SESSION['modalMessage'] = "Faça login para poder acessar esses recursos.";
            header("Location: " . BASE_URL . "/usuario/access");
            exit;
        }
    }
}