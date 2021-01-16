<?php

namespace App\Handler\User;

use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class TokenGenerator
{
    /**
     * @var TokenGeneratorInterface
     */
    private $generator;

    /**
     * ConfirmTokenGenerator constructor.
     * @param TokenGeneratorInterface $generator
     */
    public function __construct(TokenGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function generateToken(): string
    {
        return $this->generator->generateToken();
    }
}
