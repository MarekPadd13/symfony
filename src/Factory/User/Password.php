<?php


namespace App\Factory\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Password
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncode;

    /**
     * GeneratePassword constructor.
     * @param UserPasswordEncoderInterface $passwordEncode
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncode)
    {
        $this->passwordEncode = $passwordEncode;
    }
    /**
     * @param User $user
     */
    public function encode(User $user): void
    {
        $password = $this->passwordEncode->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
    }
}