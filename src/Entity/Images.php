<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\ImagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $erweiterung = null;

    #[ORM\Column(length: 255)]
    private ?string $dateipfad = null;

    #[ORM\Column(length: 255)]
    private ?string $kategorie = null;

    #[ORM\OneToOne(mappedBy: 'image', targetEntity: Hardware::class, cascade: ['persist', 'remove'])]
    private ?Hardware $hardware = null;

    public function getHardware(): ?Hardware
    {
        return $this->hardware;
    }

    public function setHardware(?Hardware $hardware): static
    {
        $this->hardware = $hardware;
        return $this;
    }

    public function __construct()
    {
//        $this->hardware = new ArrayCollection();
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

    public function getErweiterung(): ?string
    {
        return $this->erweiterung;
    }

    public function setErweiterung(string $erweiterung): static
    {
        $this->erweiterung = $erweiterung;

        return $this;
    }

    public function getDateipfad(): ?string
    {
        return $this->dateipfad;
    }

    public function setDateipfad(string $dateipfad): static
    {
        $this->dateipfad = $dateipfad;

        return $this;
    }

    public function getKategorie(): ?string
    {
        return $this->kategorie;
    }

    public function setKategorie(string $kategorie): static
    {
        $this->kategorie = $kategorie;

        return $this;
    }

}
