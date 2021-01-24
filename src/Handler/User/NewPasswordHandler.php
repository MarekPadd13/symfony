<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\PasswordEncoder;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NewPasswordHandler
{
    private PasswordEncoder $passwordEncoder;
    private UserRepository $repository;

    public function __construct(
        PasswordEncoder $passwordEncoder,
        UserRepository $repository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $repository;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $this->passwordEncoder->encode($user);
        $this->repository->save($user);
    }
}
