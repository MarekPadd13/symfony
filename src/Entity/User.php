<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository", repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    private $roles = [];

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min = 8,
     *      max = 50,
     * )
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $confirmToken;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isEnabled = false;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profile", mappedBy="user")
     *
     * @var Profile
     */
    private $profile;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preSetDateTime(): void
    {
        $dateTimeImmutable = new \DateTimeImmutable();
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt($dateTimeImmutable);
        }
        $this->setUpdatedAt($dateTimeImmutable);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getConfirmToken(): string
    {
        return $this->confirmToken;
    }

    /**
     * @param string $confirmToken
     */
    public function setConfirmToken(string $confirmToken): void
    {
        $this->confirmToken = $confirmToken;
    }

    /**
     * @return bool
     */
    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     */
    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $dateTimeImmutable
     */
    public function setCreatedAt(\DateTimeImmutable $dateTimeImmutable): void
    {
        $this->createdAt = $dateTimeImmutable;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedTime(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable $dateTimeImmutable
     */
    public function setUpdatedAt(\DateTimeImmutable $dateTimeImmutable): void
    {
        $this->updatedAt = $dateTimeImmutable;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }


    /**
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
