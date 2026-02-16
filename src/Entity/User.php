<?php

// src/Entity/User.php
namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id ;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Name darf nicht leer sein.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Der Name muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Name darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $name=null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Username darf nicht leer sein.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Der Username muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Username darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $username=null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Password darf nicht leer sein.")]
    #[Assert\Length(
        min: 6,
        max: 20,
        minMessage: "Der Password muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Password darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $password=null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "E-Mail darf nicht leer sein.")]
    private ?string $email=null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Abteilung darf nicht leer sein.")]
    private ?string $abteilung=null;

    #[ORM\Column(length: 255)]
    private ?string $zusaetzlich=null;

    #[ORM\OneToMany(targetEntity: UserRolle::class, mappedBy: "user", cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $userRoles;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEmail($email):self
    {
        $this->email = $email;
        return $this;
    }
    public function getEmail()
    {
        return $this->email;
    }

    public function setName(?string $username):self
    {
        $this->name = $username;
        return $this;
    }
    public function getName():?string
    {
        return $this->name;
    }

    public function setPassword($password):self
    {
        $this->password = $password;
        return $this;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setUsername($username):self
    {
        $this->username = $username;
        return $this;
    }
    public function getUsername():?string
    {
        return $this->username;
    }

    public function setAbteilung(?string $abteilung):self
    {
        $this->abteilung = $abteilung;
        return $this;
    }
    public function getAbteilung():?string
    {
        return $this->abteilung;
    }

    public function setZusaetzlich(?string $zusaetzlich):self
    {
        $this->zusaetzlich = $zusaetzlich;
        return $this;
    }
    public function getZusaetzlich():?string
    {
        return $this->zusaetzlich;
    }

    public function getUserIdentifier(): string
    {
        // TODO: Implement getUserIdentifier() method.
        return $this->username;
    }
    public function getRoles(): array
    {
        // Hier kannst du die Rollen aus der UserRolle-Tabelle abrufen
        $roles = [];
        foreach ($this->userRoles as $userRole) {
            $roles[$userRole->getRolle()->getId()] = 'ROLE_'.$userRole->getRolle()->getName(); // Angenommen, die Role-Entität hat eine getName()-Methode
        }
        return array_unique($roles);
    }



    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
    public function __construct()
    {
        $this->name = '';
        $this->username = '';
        $this->password = '';
        $this->email = '';
        $this->abteilung = '';
        $this->zusaetzlich = '';
        $this->userRoles = new ArrayCollection();
    }
}
