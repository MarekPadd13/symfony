<?php

namespace App\Security;

class TokenGenerator
{
    /**
     * @var int
     */
    private $number;

    /**
     * TokenGenerator constructor.
     * @param int $number
     */
    public function __construct(int $number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generate(): string
    {
        $bytes = random_bytes($this->number);

        return bin2hex($bytes);
    }
}
