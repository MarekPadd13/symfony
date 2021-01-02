<?php


namespace App\Handler\User\Registration;


use App\Entity\User;
use App\Factory\User\Mail\ConfirmationMailFactory;
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
     * @var ConfirmationMailFactory
     */
    private $confirmationMail;

    /**
     * ConfirmationMailMailer constructor.
     * @param ConfirmationMailFactory $confirmationMailFactory
     * @param MailerInterface $mailer
     */
    public function __construct(ConfirmationMailFactory $confirmationMailFactory, MailerInterface $mailer)
    {
        $this->confirmationMail = $confirmationMailFactory;
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

    /**
     * @param User $user
     * @return TemplatedEmail
     */
    private function createMessageFor(User $user): TemplatedEmail
    {
        return $this->confirmationMail->createMessageFor($user);
    }
}