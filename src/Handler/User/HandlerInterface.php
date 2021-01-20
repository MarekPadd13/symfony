<?php

namespace App\Handler\User;

use App\Entity\User;

interface HandlerInterface
{
    /**
     * @param User $user
     */
    public function handle(User $user): void;
}
