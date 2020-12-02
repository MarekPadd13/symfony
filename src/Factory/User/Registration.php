<?php


namespace App\Factory\User;


use App\Entity\User;
use App\Manager\DoctrineManager;

class Registration extends DoctrineManager
{
    /**
     * @param User $user
     */
    public function registry(User $user): void
    {
        $entityManager = $this->getObjectManager();
        $user->setStatus(false);
        $entityManager->persist($user);
        $entityManager->flush();
    }
}