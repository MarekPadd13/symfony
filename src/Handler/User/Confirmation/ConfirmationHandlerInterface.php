<?php

namespace App\Handler\User\Confirmation;

use App\Handler\HandlerInterface;

interface ConfirmationHandlerInterface extends HandlerInterface
{
    public function errorMessage(): string;
}
