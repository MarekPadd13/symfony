<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\TokenGenerator\TokenGeneratorInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordHandler
{
    private const PATH_TEMPLATE_MAIL = 'reset';
    private const SUBJECT_MAIL = 'RESET';
    private const CODE_ERROR  = 1;

    private UserRepository $repository;
    private TokenGeneratorInterface $tokenGenerator;
    private UserMailMailer $userMailMailer;
    private TranslatorInterface $translator;

    public function __construct(
        UserRepository $repository,
        TokenGeneratorInterface $tokenGenerator,
        UserMailMailer $userMailMailer,
        TranslatorInterface $translator
    ) {
        $this->userMailMailer = $userMailMailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @throws ORMException
     * @throws TransportExceptionInterface
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(User $user): void
    {
        $userOne = $this->repository->findByUserEmail($user->getEmail());
        if (!$userOne) {
            throw new \Exception($this->getErrorMessage(), self::CODE_ERROR);
        }
        $userOne->setConfirmToken($this->tokenGenerator->generate());
        $this->repository->save($userOne);
        $this->userMailMailer->sendTo($userOne, self::SUBJECT_MAIL, self::PATH_TEMPLATE_MAIL);
    }

    public function getErrorMessage(): string
    {
        return $this->translator->trans('Security.Error.email');
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Your password reset email has been sent');
    }
}
