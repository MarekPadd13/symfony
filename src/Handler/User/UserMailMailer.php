<?php

namespace App\Handler\User;

use App\Entity\User;
use App\Factory\User\Mail\MailFactory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

final class UserMailMailer
{
    private MailerInterface $mailer;
    private MailFactory $mailFactory;

    public function __construct(MailerInterface $mailer, MailFactory $mailFactory)
    {
        $this->mailer = $mailer;
        $this->mailFactory = $mailFactory;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendTo(User $user, string $subject, string $pathTemplate): void
    {
        $message = $this->createMessageFor($user, $subject, $pathTemplate);
        $this->mailer->send($message);
    }

    private function createMessageFor(User $user, string $subject, string $pathTemplate): TemplatedEmail
    {
        return $this->mailFactory->createMessageFor($user, $subject, $pathTemplate);
    }
}
