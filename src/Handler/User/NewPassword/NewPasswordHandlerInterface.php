<?php

namespace App\Handler\User\NewPassword;

use App\Handler\HandlerInterface;

interface NewPasswordHandlerInterface extends HandlerInterface
{
    public function getSuccessMessage(): string;
}
