<?php

namespace App\Handler\User\Reset;

use App\Handler\User\HandlerInterface;

interface ResetHandlerInterface extends HandlerInterface
{
    public function getSuccessMessage(): string;

    public function getErrorMessage(): string;
}
