<?php


namespace App\Security\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @throws \Exception
     */
    public function generate(): string;
}
