<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    const DRAFT = 0;
    const ACTIVE = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */

    private $password;


    /**
     * @var string The hashed hash
     * @ORM\Column(type="string")
     */

    private $hash;

    /**
     * @var string The hashed status
     * @ORM\Column(type="boolean")
     */

    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $created_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $updated_time;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getStatusName(): ?string
    {
        $array = ['DRAFT', 'ACTIVE'];
        return $array[$this->getStatus()];
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCreatedTime(): ?int
    {
       return $this->created_time;
    }

    /**
     * @param int $time
     * @return $this
     */
    public function setCreatedTime(int $time): self
    {
        $this->created_time = $time;

        return $this;
    }


    /**
     * @param int $time
     * @return $this
     */
    public function setUpdatedTime(int $time): self
    {
        $this->updated_time = $time;

        return $this;
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
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
