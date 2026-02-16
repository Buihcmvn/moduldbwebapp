<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\RolleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RolleRepository::class)]
class Rolle
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Name darf nicht leer sein.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Der Name muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Name darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $beschreibung = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kommentar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): int
    {
        $this->id = $id;
        return $this;
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

    public function getBeschreibung(): ?string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(?string $beschreibung): self
    {
        $this->beschreibung = $beschreibung;
        return $this;
    }

    public function getKommentar(): ?string
    {
        return $this->kommentar;
    }

    public function setKommentar(?string $kommentar): self
    {
        $this->kommentar = $kommentar;
        return $this;
    }
}

