<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\ProjekteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjekteRepository::class)]
class Projekte
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Projekt Name darf nicht leer sein.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Der Projekt Name muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Projekt Name darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $name = null;

    #[Assert\NotBlank(message: "Projekt Area ID darf nicht leer sein.")]
    #[ORM\ManyToMany(targetEntity: Area::class, inversedBy: 'projekte')]
    private Collection $area;

    #[Assert\NotBlank(message: "Projekt Hardware ID darf nicht leer sein.")]
    #[ORM\ManyToMany(targetEntity: Hardware::class, inversedBy: 'projekte')]
    private Collection $hardware;

    #[Assert\NotBlank(message: "Projekt Software ID darf nicht leer sein.")]
    #[ORM\ManyToMany(targetEntity: Software::class, inversedBy: 'projekte')]
    private Collection $software;

    public function __construct(){
        $this->area = new ArrayCollection();
        $this->hardware = new ArrayCollection();
        $this->software = new ArrayCollection();
    }

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $beschreibung = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kommentar = null;

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

    /**
     * @return Collection<int, Area>
     */
    public function getArea(): Collection
    {
        return $this->area;
    }

    public function addArea(Area $area): static
    {
        if (!$this->area->contains($area)) {
            $this->area->add($area);
            $area->addProjekte($this);  // Important: Keep both sides in sync
        }
        return $this;
    }

    public function removeArea(Area $area): static
    {
        if($this->area->contains($area)){
            $this->area->removeElement($area);
            $area->removeProjekte($this); // Important: Keep both sides in sync
        }
        return $this;
    }

    public function getHardware(): Collection
    {
        return $this->hardware;
    }

    public function addHardware(Hardware $hardware): static
    {
        if (!$this->hardware->contains($hardware)) {
            $this->hardware->add($hardware);
            $hardware->addProjekte($this); // Important: Keep both sides in sync
        }
        return $this;
    }

    public function removeHardware(Hardware $hardware): static
    {
        if($this->hardware->contains($hardware)){
            $this->hardware->removeElement($hardware);
            $hardware->removeProjekte($this); // Important: Keep both sides in sync
        }
        return $this;
    }

    public function getSoftware(): Collection
    {
        return $this->software;
    }

    public function addSoftware(Software $software): static
    {
        if (!$this->software->contains($software)) {
            $this->software->add($software);
            $software->addProjekte($this); // Important: Keep both sides in sync
        }
        return $this;
    }

    public function removeSoftware(Software $software): static
    {
        if($this->software->contains($software)){
            $this->software->removeElement($software);
            $software->removeProjekte($this); // Important: Keep both sides in sync
        }
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

    public function getKommentar(): ?string
    {
        return $this->kommentar;
    }

    public function setKommentar(?string $kommentar): static
    {
        $this->kommentar = $kommentar;
        return $this;
    }
}
