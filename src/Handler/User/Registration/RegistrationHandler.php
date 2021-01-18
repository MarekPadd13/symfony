<?php

namespace App\Handler\User\Registration;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\TokenGenerator;
use App\Service\UserPasswordEncoder;
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
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

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
     * @param ConfirmationMailMailer $confirmationMailMailer
     * @param UserPasswordEncoder $passwordEncoder
     * @param UserRepository $repository
     * @param TokenGenerator $tokenGenerator
     * @param TranslatorInterface $translator
     */
    public function __construct(ConfirmationMailMailer $confirmationMailMailer,
                                UserPasswordEncoder $passwordEncoder,
                                UserRepository $repository,
                                TokenGenerator $tokenGenerator,
                                TranslatorInterface $translator)
    {
        $this->confirmationMailMailer = $confirmationMailMailer;
        $this->passwordEncoder = $passwordEncoder;
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
        $user->setConfirmToken($this->tokenGenerator->generateToken());
        $this->passwordEncoder->encode($user);
        $this->repository->save($user, true);
        $this->confirmationMailMailer->sendTo($user);
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Confirm your email');
    }
}
