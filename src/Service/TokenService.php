<?php

namespace App\Service;

class TokenService
{
    //On génère le token

    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        //generates a crypto-secure 32 characters long
        return bin2hex(random_bytes(16));
    }

}