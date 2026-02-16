<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\RechtRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RechtRepository::class)]
class Recht
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Recht_Name darf nicht leer sein.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Der Recht_Name muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Recht_Name darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $recht_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recht_beschreibung = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getRechtName(): ?string
    {
        return $this->recht_name;
    }

    public function setRechtName(string $recht_name): static
    {
        $this->recht_name = $recht_name;
        return $this;
    }

    public function getRechtBeschreibung(): ?string
    {
        return $this->recht_beschreibung;
    }

    public function setRechtBeschreibung(?string $recht_beschreibung): static
    {
        $this->recht_beschreibung = $recht_beschreibung;
        return $this;
    }
}
