<?php

namespace App\Handler\User\Registration;

use App\Entity\User;
use App\Handler\User\TokenGenerator;
use App\Repository\UserRepository;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationHandler implements RegistrationHandlerInterface
{
    /**
     * @var ConfirmationMailMailer
     */
    private $confirmationMailMailer;

    /**
     * @var Password
     */
    private $password;

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
     */
    public function __construct(ConfirmationMailMailer $confirmationMailMailer,
                                Password $password,
                                UserRepository $repository,
                                TokenGenerator $tokenGenerator,
                                TranslatorInterface $translator)
    {
        $this->confirmationMailMailer = $confirmationMailMailer;
        $this->password = $password;
        $this->tokenGenerator = $tokenGenerator;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws TransportExceptionInterface
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(User $user): void
    {
        $user->setConfirmToken($this->tokenGenerator->generateToken());
        $this->password->encode($user);
        $this->repository->save($user, true);
        $this->confirmationMailMailer->sendTo($user);
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Confirm your email');
    }
}
