<?php


namespace App\Handler\Profile;

use App\Entity\Profile as ProfileEntity;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileHandler implements ProfileHandlerInterface
{
    /**
     * @var Profile
     */
    private $profile;
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * ProfileHandle constructor.
     * @param Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @param ProfileEntity $entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function handle($entity)
    {
        $this->profile->createOrEdit($entity, $this->getUser());
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}