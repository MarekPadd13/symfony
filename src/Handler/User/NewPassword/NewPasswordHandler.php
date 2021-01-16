<?php

namespace App\Handler\User\NewPassword;

use App\Entity\User;
use App\Handler\User\Registration\Password;
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
     * @var Password
     */
    private $password;

    /**
     * ConfirmationHandler constructor.
     * @param UserRepository $repository
     * @param TranslatorInterface $translator
     */
    public function __construct(UserRepository $repository, Password $password, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->translator = $translator;
        $this->password = $password;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $this->password->encode($user);
        $this->repository->save($user);
    }


    /**
     * @return string
     */
    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Your status is active');
    }
}
