<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{

    private $managerRegistry;
    private $passwordEncode;
    private $mailer;

    /**
     * UserService constructor.
     * @param ManagerRegistry $managerRegistry
     * @param UserPasswordEncoderInterface $passwordEncode
     * @param MailerInterface $mailer
     */
    public function __construct(ManagerRegistry $managerRegistry,
                                UserPasswordEncoderInterface $passwordEncode,
                                MailerInterface $mailer)
    {
        $this->managerRegistry = $managerRegistry->getManager();
        $this->passwordEncode = $passwordEncode;
        $this->mailer = $mailer;
    }


    /**
     * @param User $user
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function registry(User $user): void
    {
        $entityManager = $this->managerRegistry;
        $this->hashGenerated($user);
        $this->passwordEncode($user);
        $user->setCreatedTime(time());
        $this->setUpdatedTime($user);
        $user->setStatus($user::DRAFT);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->sendMail($user);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function confirm(User $user): void
    {
        if($user->getStatus()) {
            throw new \Exception("Your status is active");
        }
        $this->setUpdatedTime($user);
        $user->setStatus($user::ACTIVE);
        $this->managerRegistry->flush();
    }

    /**
     * @param User $user
     */
    private function passwordEncode(User $user): void
    {
        $password = $this->passwordEncode->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
    }

    /**
     * @param User $user
     */
    private function hashGenerated(User $user): void
    {
        $hash = hash('md4', $user->getEmail().":".$user->getPassword());
        $user->setHash($hash);
    }

    /**
     * @param User $user
     */
    private function setUpdatedTime(User $user):void
    {
        $user->setUpdatedTime(time());
    }

    /**
     * @param User $user
     * @throws TransportExceptionInterface
     */

    private function sendMail(User $user): void
    {
        $message = (new TemplatedEmail())
            ->from('cpk@mpgu.edu')
            ->to($user->getEmail())
            ->subject("Hello")
            ->htmlTemplate('email/registration/index.html.twig')
            ->textTemplate('email/registration/index.txt.twig')
            ->context(
                ['user' => $user]
            );

        $this->mailer->send($message);
    }



}