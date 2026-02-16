<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
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

    #[ORM\ManyToMany(targetEntity: Projekte::class, mappedBy: 'area')]
    private Collection $projekte;

    public function __construct()
    {
        $this->projekte = new ArrayCollection();
    }

    public function getProjekte(): Collection
    {
        return $this->projekte;
    }

    public function addProjekte(Projekte $projekte): static
    {
        if(!$this->projekte->contains($projekte)) {
            $this->projekte->add($projekte);
        }
        return $this;
    }

    public function removeProjekte(Projekte $projekte): static
    {
        if($this->projekte->contains($projekte)) {
            $this->projekte->removeElement($projekte);
        }
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getBeschreibung(): ?string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(?string $beschreibung): static
    {
        $this->beschreibung = $beschreibung;
        return $this;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'beschreibung' => $this->getBeschreibung(),
        ];
    }

    public function __toString(): string
    {
        return ($this->getName())??'';
    }
}
