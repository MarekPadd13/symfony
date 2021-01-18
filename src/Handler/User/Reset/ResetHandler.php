<?php

namespace App\Handler\User\Reset;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\TokenGenerator;
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
        $userOne = $this->repository->findByUserEmail($user->getEmail());
        if (!$userOne) {
            throw new \Exception($this->getErrorMessage());
        }
        $userOne->setConfirmToken($this->tokenGenerator->generateToken());
        $this->repository->save($userOne);
        $this->resetMailMailer->sendTo($userOne);
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Your password reset email has been sent');
    }

    public function getErrorMessage(): string
    {
        return $this->translator->trans('Security.Error.email');
    }
}
