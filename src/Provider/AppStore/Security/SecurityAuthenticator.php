<?php

namespace App\Provider\AppStore\Security;

use App\Provider\AuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;

class SecurityAuthenticator implements AuthenticatorInterface
{
    public function isValid(Request $request): bool
    {
        return true;
    }
}
