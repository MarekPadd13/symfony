<?php

namespace App\Handler\User\Registration;

use App\Entity\User;
use App\Factory\User\Mail\MailFactory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ConfirmationMailMailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var MailFactory
     */
    private $mailFactory;

    /**
     * ConfirmationMailMailer constructor.
     * @param MailFactory $confirmationMailFactory
     * @param MailerInterface $mailer
     */
    public function __construct(MailFactory $mailFactory, MailerInterface $mailer)
    {
        $this->mailFactory = $mailFactory;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendTo(User $user): void
    {
        $message = $this->createMessageFor($user);
        $this->mailer->send($message);
    }

    private function createMessageFor(User $user): TemplatedEmail
    {
        return $this->mailFactory->createMessageFor($user, 'Hello', 'registration');
    }
}
