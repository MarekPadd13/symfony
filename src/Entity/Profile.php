<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 * @ORM\Table(name="profile")
 */
class Profile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, name="last_name")
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=180, name="first_name")
     * *@Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $patronymic;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=15)
     */
    private $phone;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer", length=1)
     */
    private $sex;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Profile constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preSetUser(): void
    {
        $this->setUser($this->user);
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return $this
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return $this
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param $this
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return $this
     */
    public function getPatronymic(): ?string
    {
        return $this->patronymic;
    }

    /**
     * @param $patronymic
     */
    public function setPatronymic($patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    public function getSex(): ?int
    {
        return $this->sex;
    }

    /**
     * @param $sex
     */
    public function setSex($sex): void
    {
        $this->sex = $sex;
    }

    /**
     * @return $this
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }
}
