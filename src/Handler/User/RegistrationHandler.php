<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\TokenGenerator\TokenGeneratorInterface;
use App\Security\PasswordEncoder;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegistrationHandler
{
    private const PATH_TEMPLATE_MAIL = 'registration';
    private const SUBJECT_MAIL = 'HELLO';

    private TokenGeneratorInterface $tokenGenerator;
    private PasswordEncoder $passwordEncoder;
    private UserRepository $repository;
    private UserMailMailer $userMailMailer;

    public function __construct(
        TokenGeneratorInterface $tokenGenerator,
        PasswordEncoder $passwordEncoder,
        UserRepository $repository,
        UserMailMailer $userMailMailer
    ) {
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $repository;
        $this->userMailMailer = $userMailMailer;
    }

    /**
     * @throws ORMException
     * @throws TransportExceptionInterface
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $user->setConfirmToken($this->tokenGenerator->generate());
        $this->passwordEncoder->encode($user);
        $this->repository->save($user);
        $this->userMailMailer->sendTo($user, self::SUBJECT_MAIL, self::PATH_TEMPLATE_MAIL);
    }
}
