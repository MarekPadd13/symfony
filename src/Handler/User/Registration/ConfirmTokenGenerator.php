<?php

namespace App\Handler\User\Registration;

use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ConfirmTokenGenerator
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

    public function generateConfirmToken(): string
    {
        return $this->generator->generateToken();
    }
}
