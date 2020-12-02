<?php


namespace App\Factory\User;


use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ConfirmationMailMailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var ConfirmationMail
     */
    private $confirmationMail;

    /**
     * ConfirmationMailMailer constructor.
     * @param ConfirmationMail $confirmationMail
     * @param MailerInterface $mailer
     */
    public function __construct(ConfirmationMail $confirmationMail, MailerInterface $mailer)
    {
        $this->confirmationMail = $confirmationMail;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendTo(User $user): void
    {
        $message = $this->createMessageFor($user);
        $this->mailer->send($message);
    }

    /**
     * @param User $user
     * @return TemplatedEmail
     */
    private function createMessageFor(User $user): TemplatedEmail
    {
        return $this->confirmationMail->createMessageFor($user);
    }
}