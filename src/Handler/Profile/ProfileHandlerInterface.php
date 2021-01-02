<?php


namespace App\Handler\Profile;

use App\Handler\HandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ProfileHandlerInterface extends HandlerInterface
{
    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser():UserInterface;
}