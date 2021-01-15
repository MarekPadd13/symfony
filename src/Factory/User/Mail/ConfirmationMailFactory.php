<?php

namespace App\Factory\User\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ConfirmationMailFactory
{
    /**
     * @var ContainerBagInterface
     */
    private $container;

    /**
     * ConfirmationMailFactory constructor.
     */
    public function __construct(ContainerBagInterface $container)
    {
        $this->container = $container;
    }

    public function createMessageFor(User $user): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($this->container->get('app.email_admin'))
            ->to($user->getEmail())
            ->subject('Hello')
            ->htmlTemplate('email/registration/index.html.twig')
            ->textTemplate('email/registration/index.txt.twig')
            ->context(['user' => $user]);
    }
}
