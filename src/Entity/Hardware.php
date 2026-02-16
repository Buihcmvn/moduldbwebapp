<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\HardwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HardwareRepository::class)]
class Hardware
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Hardware Name darf nicht leer sein.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Der Hardware Name muss mindestens {{ limit }} Zeichen lang sein.",
        maxMessage: "Der Hardware Name darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Bezeichnen darf nicht leer sein.")]
    #[Assert\Length(
        max: 9,
        maxMessage: "Der Bezeichnen darf maximal {{ limit }} Zeichen lang sein."
    )]
    private ?string $bezeichnen = null;

    #[ORM\Column(length: 1000)]
    private ?string $beschreibung = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $kommentar = null;

    // OneToOne
    #[ORM\OneToOne(targetEntity: Images::class, cascade: ['persist', 'remove'], inversedBy: 'hardware')]
    #[ORM\JoinColumn(nullable: true)] // Allow hardware doesnt have image
    private ?Images $image = null;


    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'hardware')]
    private Collection $file;

    #[ORM\ManyToMany(targetEntity: Projekte::class, mappedBy: 'hardware')]
    private Collection $projekte;


    public function __construct()
    {
        $this->file = new ArrayCollection();
        $this->projekte = new ArrayCollection();
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

    public function getBezeichnen(): ?string
    {
        return $this->bezeichnen;
    }

    public function setBezeichnen(string $bezeichnen): static
    {
        $this->bezeichnen = $bezeichnen;
        return $this;
    }

    public function getBeschreibung(): ?string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(string $beschreibung): static
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

    /**
     * @return Collection<int, File>
     */
    public function getFile(): Collection
    {
        return $this->file;
    }

    public function addFile(File $file): static
    {
        if (!$this->file->contains($file)) {
            $this->file->add($file);
            $file->setHardware($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->file->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getHardware() === $this) {
                $file->setHardware(null);
            }
        }
        return $this;
    }

    public function getProjekte(): Collection
    {
        return $this->projekte;
    }

    public function addProjekte(Projekte $projekte): static
    {
        if (!$this->projekte->contains($projekte)) {
            $this->projekte->add($projekte);
        }
        return $this;
    }

    public function removeProjekte(Projekte $projekte): static
    {
        if ($this->projekte->removeElement($projekte)) {
            $this->projekte->removeElement($projekte);
        }
        return $this;
    }

    public function setImage(Images $image):static
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): ?Images
    {
        return $this->image;
    }

//    public function __toString(): string
//    {
//        return ($this->getName())??'';
//    }
}
