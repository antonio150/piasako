<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class WebAccessDeniedException extends AuthenticationException
{
    public function getMessageKey(): string
    {
        return 'Accès web non autorisé pour cet utilisateur.';
    }
}