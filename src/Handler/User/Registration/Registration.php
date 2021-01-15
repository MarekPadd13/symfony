<?php

namespace App\Handler\User\Registration;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\ORMException;

class Registration extends UserRepository
{
    /**
     * @param User $user
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registry(User $user): void
    {
        $this->generateConfirmToken($user);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param User $user
     */
    private function generateConfirmToken(User $user): void
    {
        $token = hash('md4', $user->getEmail().':'.$user->getPassword());
        $user->setConfirmToken($token);
    }
}
