<?php


namespace App\Handler\User\Confirmation;

use App\Handler\HandlerInterface;

interface ConfirmationHandlerInterface extends HandlerInterface
{
    /**
     * @return string
     */
    public function errorMessage(): string;
}