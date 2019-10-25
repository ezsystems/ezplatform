<?php

namespace App\Context;

class AuthenticationContextReplacer extends AuthenticationContext
{
    public function iLoginAs(string $username, string $password): void
    {
        throw new \Exception('I am a replacer!');
    }
}