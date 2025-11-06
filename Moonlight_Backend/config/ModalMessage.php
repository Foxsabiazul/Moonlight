<?php

/**
 * classe pra entregar e receber mensagens de erro personalizados.
 * usar apenas para tratar regras de negocio.
 */

namespace Moonlight_Backend\Config;

class ModalMessage 
{
    private string $title;
    private string $message;

    public function __construct(string $title, string $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}