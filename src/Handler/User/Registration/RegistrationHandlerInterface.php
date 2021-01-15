<?php

namespace App\Handler\User\Registration;

use App\Handler\HandlerInterface;

interface RegistrationHandlerInterface extends HandlerInterface
{
    public function successMessage(): string;
}
