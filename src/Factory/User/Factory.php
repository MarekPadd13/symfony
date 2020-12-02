<?php


namespace App\Factory\User;


use App\Entity\User;

class Factory
{
    /**
     * @var Registration
     */
    private $registration;

    /**
     * @var Confirmation
     */
    private $confirmation;

    /**
     * @var ConfirmationMailMailer
     */
    private $confirmationMailMailer;

    /**
     * @var Password
     */
    private $password;

    /**
     * Factory constructor.
     * @param Registration $registration
     * @param ConfirmationMailMailer $confirmationMailMailer
     * @param Confirmation $confirmation
     * @param Password $password
     */
    public function __construct(Registration $registration, ConfirmationMailMailer $confirmationMailMailer,
                                Confirmation $confirmation, Password $password)
    {
        $this->confirmation = $confirmation;
        $this->registration = $registration;
        $this->confirmationMailMailer = $confirmationMailMailer;
        $this->password = $password;
    }

    /**
     * @param User $user
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function createTo(User $user): void
    {
        $this->password->encode($user);
        $this->registration->registry($user);
        $this->confirmationMailMailer->sendTo($user);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function confirmed(User $user): void
    {
        $this->confirmation->confirm($user);
    }
}