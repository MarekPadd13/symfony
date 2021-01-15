<?php

namespace App\Handler\Profile;

use App\Handler\HandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ProfileHandlerInterface extends HandlerInterface
{
    /**
     * @return mixed
     */
    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;
}
