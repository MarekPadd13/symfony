<?php

namespace App\Handler\Profile;

use App\Entity\Profile as ProfileEntity;
use App\Repository\ProfileRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\User\UserInterface;

class Profile extends ProfileRepository
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createOrEdit(ProfileEntity $entity, UserInterface $user): void
    {
        if (!$entity->getUser()) {
            $entity->setUser($user);
            $this->_em->persist($entity);
        }
        $this->_em->flush();
    }
}
