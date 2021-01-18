<?php

namespace App\Handler\User\Registration;

use App\Handler\User\HandlerInterface;

interface RegistrationHandlerInterface extends HandlerInterface
{
    public function getSuccessMessage(): string;
}
