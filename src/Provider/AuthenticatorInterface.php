<?php

namespace App\Provider;

use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    public function isValid(Request $request): bool;
}
