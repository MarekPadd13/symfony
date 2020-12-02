<?php


namespace App\Factory\User;


use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ConfirmationMail
{
    /**
     * @param User $user
     * @return TemplatedEmail
     */
    public function createMessageFor(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from('cpk@mpgu.edu')
            ->to($user->getEmail())
            ->subject("Hello")
            ->htmlTemplate('email/registration/index.html.twig')
            ->textTemplate('email/registration/index.txt.twig')
            ->context(['user' => $user]);
    }
}