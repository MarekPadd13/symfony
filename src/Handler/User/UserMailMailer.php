<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Factory\User\Mail\MailFactory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserMailMailer
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
     * @param MailFactory $mailFactory
     * @param MailerInterface $mailer
     */
    public function __construct(MailFactory $mailFactory, MailerInterface $mailer)
    {
        $this->mailFactory = $mailFactory;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $pathTemplate
     * @throws TransportExceptionInterface
     */
    public function sendTo(User $user, string $subject, string $pathTemplate): void
    {
        $message = $this->createMessageFor($user, $subject, $pathTemplate);
        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $pathTemplate
     * @return TemplatedEmail
     */
    private function createMessageFor(User $user, string $subject, string $pathTemplate): TemplatedEmail
    {
        return $this->mailFactory->createMessageFor($user, $subject, $pathTemplate);
    }
}
