<?php

namespace App\Factory\User\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailFactory
{
    private string $adminEmail;

    public function __construct(string $adminEmail)
    {
        $this->adminEmail = $adminEmail;
    }

    public function createMessageFor(User $user, string $subject, string $pathTemplate): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($this->adminEmail)
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate('email/' . $pathTemplate . '/index.html.twig')
            ->textTemplate('email/' . $pathTemplate . '/index.txt.twig')
            ->context(['user' => $user]);
    }
}
