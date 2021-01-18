<?php

namespace App\Handler\User\Confirmation;

use App\Handler\User\HandlerInterface;

interface ConfirmationHandlerInterface extends HandlerInterface
{
    public function getSuccessMessage(): string;

    public function getErrorMessage(): string;
}
