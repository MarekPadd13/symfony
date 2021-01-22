<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ConfirmationHandler
{
    private UserRepository $repository;
    private TranslatorInterface $translator;

    public function __construct(UserRepository $repository, TranslatorInterface $translator)
    {
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
        if ($user->getIsEnabled()) {
            throw new \Exception($this->getErrorMessage(), 422);
        }
        $user->setIsEnabled(true);
        $this->repository->save($user);
    }

    public function getErrorMessage(): string
    {
        return $this->translator->trans('Your status is active');
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Your email is confirmed');
    }
}
