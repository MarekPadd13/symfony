<?php

namespace App\Security\TokenGenerator;

class TokenGenerator implements TokenGeneratorInterface
{
    private const DEFAULT_NUMBER = 32;
    private int $number;

    public function __construct(int $number = self::DEFAULT_NUMBER)
    {
        $this->number = $number;
    }

    /**
     * @throws \Exception
     */
    public function generate(): string
    {
        $bytes = random_bytes($this->number);

        return bin2hex($bytes);
    }
}
