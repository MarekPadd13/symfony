<?php


namespace App\Factory\User;

use App\Entity\User;
use App\Manager\DoctrineManager;

class Confirmation extends DoctrineManager
{
    /**
     * @param User $user
     * @throws \Exception
     */
    public function confirm(User $user): void
    {
        if($user->getStatus()) {
            throw new \Exception("Your status is active");
        }
        $user->setStatus(true);
        $this->getObjectManager()->flush();
    }

}