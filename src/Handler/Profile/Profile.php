<?php


namespace App\Handler\Profile;


use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Profile as ProfileEntity;
use App\Repository\ProfileRepository;

class Profile extends ProfileRepository
{
    /**
     * @param ProfileEntity $entity
     * @param UserInterface $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createOrEdit(ProfileEntity $entity, UserInterface $user):void
    {
        if(!$entity->getUser()){
            $entity->setUser($user);
            $this->_em->persist($entity);
        }
        $this->_em->flush();
    }
}