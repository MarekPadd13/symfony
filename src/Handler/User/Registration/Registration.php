<?php

namespace App\Handler\User\Registration;


use App\Entity\User;
use App\Handler\User\HandlerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class Registration extends UserRepository
{
    /**
     * @param User $user
     * @throws ORMException
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
        $token = hash('md4', $user->getEmail().":".$user->getPassword());
        $user->setConfirmToken($token);
    }

}