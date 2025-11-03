<?php

namespace Moonlight_Backend\Controller;
/**
 * namespace serve pra definir caminhos para autoload de classes e para nao se confundir com metódos publicos do php. (o Composer precisa disso!)
 */


use Moonlight_Backend\Controller\Controller;

final class UsuarioController extends Controller
{


        /**
         * FAZER LOGOUT:
         */     
                /**
                 * metodo de rota para fazer logout
                 */
                public function logoutUsuario()
                {
                    //limpa a sessão
                    $this->limparSessão();

                    //destrói a sessão
                    $this->destruirSessão();

                    header("Location: /login");
                    exit;
                }

                private function limparSessão()
                {
                    // Limpa todas as variáveis de sessão
                    $_SESSION = [];
                }

                private function destruirSessão()
                {
                    session_destroy();
                }
}