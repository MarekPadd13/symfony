<?php

namespace App\Service;

class TokenGenerator
{
    /**
     * @throws \Exception
     */
    public function generateToken(): string
    {
        $bytes = random_bytes(32);

        return bin2hex($bytes);
    }
}
