<?php

namespace App\Service;

class Token
{
    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        $bytes = random_bytes(32);

        return bin2hex($bytes);
    }
}
