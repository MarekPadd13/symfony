<?php


namespace App\Handler\User\Confirmation;


use App\Entity\User;
use App\Repository\UserRepository;

class Confirmation extends UserRepository
{
    /**
     * @param User $user
     * @param $message
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function confirm(User $user, $message): void
    {
        if ($user->getIsEnabled()) {
            throw new \Exception($message);
        }
        $user->setIsEnabled(true);
        $this->_em->flush();
    }
}