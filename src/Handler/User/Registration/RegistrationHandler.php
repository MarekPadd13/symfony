<?php


namespace App\Handler\User\Registration;


use App\Entity\User;
use Doctrine\ORM\ORMException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegistrationHandler implements  RegistrationHandlerInterface
{
    /**
     * @var Registration
     */
    private $registration;

    /**
     * @var ConfirmationMailMailer
     */
    private $confirmationMailMailer;

    /**
     * @var Password
     */
    private $password;

    /**
     * @param Registration $registration
     * @param ConfirmationMailMailer $confirmationMailMailer
     * @param Password $password
     */
    public function __construct(Registration $registration, ConfirmationMailMailer $confirmationMailMailer, Password $password)
    {
        $this->registration = $registration;
        $this->confirmationMailMailer = $confirmationMailMailer;
        $this->password = $password;
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws TransportExceptionInterface
     */
    public function handle($user)
    {
         $this->password->encode($user);
         $this->registration->registry($user);
         $this->confirmationMailMailer->sendTo($user);
    }

    /**
     * @return string
     */
    public function successMessage(): string
    {
        return "Confirm your email";
    }
}