<?php

namespace App\Handler\User\Registration;

use App\Entity\User;
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
     * @var ConfirmTokenGenerator
     */
    private $confirmTokenGenerator;

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
     *
     * @param ConfirmationMailMailer $confirmationMailMailer
     * @param Password $password
     * @param UserRepository $repository
     * @param ConfirmTokenGenerator $confirmTokenGenerator
     */
    public function __construct(ConfirmationMailMailer $confirmationMailMailer,
                                Password $password,
                                UserRepository $repository,
                                ConfirmTokenGenerator $confirmTokenGenerator,
                                TranslatorInterface $translator)
    {
        $this->confirmationMailMailer = $confirmationMailMailer;
        $this->password = $password;
        $this->confirmTokenGenerator = $confirmTokenGenerator;
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
        $user->setConfirmToken($this->confirmTokenGenerator->generateConfirmToken());
        $this->password->encode($user);
        $this->repository->save($user, true);
        $this->confirmationMailMailer->sendTo($user);
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Confirm your email');
    }
}
