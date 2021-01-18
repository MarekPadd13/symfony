<?php

namespace App\Handler\User\NewPassword;

use App\Handler\User\HandlerInterface;

interface NewPasswordHandlerInterface extends HandlerInterface
{
    public function getSuccessMessage(): string;
}
