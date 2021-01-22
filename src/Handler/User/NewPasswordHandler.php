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
    private TranslatorInterface $translator;

    public function __construct(
        PasswordEncoder $passwordEncoder,
        UserRepository $repository,
        TranslatorInterface $translator
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $repository;
        $this->translator = $translator;
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

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Your password success upgraded');
    }
}
