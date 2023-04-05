<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Assert\NotBlank(message="Email cannot be blank")
     * @Assert\Email(message="The email {{ value }} is not a valid email.")
     *
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     * @Assert\NotBlank(message="Password cannot be blank")
     * @Assert\Length(min=6)
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @Assert\NotBlank(message="Name cannot be blank")
     * @Assert\Regex(pattern="/\d/",match=false,message="Name cannot contain a number")
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @Assert\NotBlank(message="Phone number cannot be blank")
     * @Assert\Regex(pattern="/\d/",match=true,message="Phone number cannot contain a alphabets")
     * @Assert\Length(min=10,max=16)
     */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone_number = null;

    /**
     * @Assert\NotBlank(message="Please select one option")
     */
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $gender = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profile_image = null;

    /**
     * @Assert\NotBlank(message="Please select at least one option")
     */
    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $hobbies = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): self
    {
        $this->profile_image = $profile_image;

        return $this;
    }

    public function getHobbies(): array
    {
        return $this->hobbies;
    }

    public function setHobbies(?array $hobbies): self
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
