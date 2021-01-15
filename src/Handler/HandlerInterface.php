<?php

namespace App\Handler;

use App\Entity\User;

interface HandlerInterface
{
    /**
     * @param User $user
     * @return mixed
     */
    public function handle(User $user): void;
}
