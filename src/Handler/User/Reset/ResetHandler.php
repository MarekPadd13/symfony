<?php

namespace App\Handler\User\Reset;

use App\Entity\User;
use App\Handler\User\TokenGenerator;
use App\Repository\UserRepository;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetHandler implements ResetHandlerInterface
{
    /**
     * @var ResetMailMailer
     */
    private $resetMailMailer;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * RegistrationHandler constructor.
     * @param ResetMailMailer $resetMailMailer
     * @param UserRepository $repository
     * @param TokenGenerator $tokenGenerator
     * @param TranslatorInterface $translator
     */
    public function __construct(ResetMailMailer $resetMailMailer,
                                UserRepository $repository,
                                TokenGenerator $tokenGenerator,
                                TranslatorInterface $translator)
    {
        $this->resetMailMailer = $resetMailMailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws TransportExceptionInterface
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $userOne = $this->repository->findOneBy(['email' => $user->getEmail()]);
        $this->exception($userOne);
        $userOne->setConfirmToken($this->tokenGenerator->generateToken());
        $this->repository->save($userOne);
        $this->resetMailMailer->sendTo($userOne);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    private function exception(User $user)
    {
        if (!$user) {
            throw new \Exception($this->getErrorMessage());
        }
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Confirm your email');
    }

    public function getErrorMessage(): string
    {
        return 'suka';
    }
}
