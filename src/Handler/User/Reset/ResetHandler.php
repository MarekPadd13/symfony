<?php

namespace App\Handler\User\Reset;

use App\Entity\User;
use App\Handler\User\UserMailMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetHandler implements ResetHandlerInterface
{
    const PATH_TEMPLATE_MAIL = 'reset';
    const SUBJECT_MAIL = 'RESET';
    const NUMBER_TOKEN_GENERATOR = 32;
    /**
     * @var UserMailMailer
     */
    private $userMailMailer;

    /**
     * @var TokenGenerator
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
     * @param UserRepository $repository
     * @param TranslatorInterface $translator
     */
    public function __construct(UserMailMailer $userMailMailer,
                                UserRepository $repository,
                                TranslatorInterface $translator)
    {
        $this->userMailMailer = $userMailMailer;
        $this->token = new TokenGenerator(self::NUMBER_TOKEN_GENERATOR);
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
            throw new \Exception($this->getErrorMessage(), 422);
        }
        $userOne->setConfirmToken($this->token->generate());
        $this->repository->save($userOne);
        $this->userMailMailer->sendTo($userOne, self::SUBJECT_MAIL, self::PATH_TEMPLATE_MAIL);
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
