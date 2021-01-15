<?php

namespace App\Handler\User\Confirmation;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfirmationHandler implements ConfirmationHandlerInterface
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
     * ConfirmationHandler constructor.
     * @param UserRepository $repository
     * @param TranslatorInterface $translator
     */
    public function __construct(UserRepository $repository, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $this->exception($user);
        $user->setIsEnabled(true);
        $this->repository->save($user);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    private function exception(User $user)
    {
        if ($user->getIsEnabled()) {
            throw new \Exception($this->getErrorMessage());
        }
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->translator->trans('Your status is active');
    }
}
