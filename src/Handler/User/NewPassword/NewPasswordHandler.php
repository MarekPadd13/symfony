<?php

namespace App\Handler\User\NewPassword;

use App\Entity\User;
use App\Service\UserPasswordEncoder;
use App\Repository\UserRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewPasswordHandler implements NewPasswordHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * ConfirmationHandler constructor.
     * @param UserRepository $repository
     * @param UserPasswordEncoder $passwordEncoder
     * @param TranslatorInterface $translator
     */
    public function __construct(UserRepository $repository, UserPasswordEncoder $passwordEncoder, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->translator = $translator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $this->passwordEncoder->encode($user);
        $this->repository->save($user);
    }

    /**
     * @return string
     */
    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Your password success upgraded');
    }
}
