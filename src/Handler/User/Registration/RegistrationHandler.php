<?php

namespace App\Handler\User\Registration;

use App\Entity\User;
use App\Handler\User\UserMailMailer;
use App\Repository\UserRepository;
use App\Service\Token;
use App\Service\UserPasswordEncoder;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationHandler implements RegistrationHandlerInterface
{
    const PATH_TEMPLATE_MAIL = 'registration';
    const SUBJECT_MAIL = 'HELLO';
    /**
     * @var UserMailMailer
     */
    private $userMailMailer;

    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * @var Token
     */
    private $token;

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
     * @param UserMailMailer $userMailMailer
     * @param UserPasswordEncoder $passwordEncoder
     * @param UserRepository $repository
     * @param Token $token
     * @param TranslatorInterface $translator
     */
    public function __construct(UserMailMailer $userMailMailer,
                                UserPasswordEncoder $passwordEncoder,
                                UserRepository $repository,
                                Token $token,
                                TranslatorInterface $translator)
    {
        $this->userMailMailer = $userMailMailer;
        $this->passwordEncoder = $passwordEncoder;
        $this->token = $token;
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
        $user->setConfirmToken($this->token->generate());
        $this->passwordEncoder->encode($user);
        $this->repository->save($user, true);
        $this->userMailMailer->sendTo($user, self::SUBJECT_MAIL, self::PATH_TEMPLATE_MAIL);
    }

    public function getSuccessMessage(): string
    {
        return $this->translator->trans('Confirm your email');
    }
}
