<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $encryptedPath = null;

    #[ORM\ManyToOne(inversedBy: 'file')]
    private ?Hardware $hardware = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEncryptedPath(): ?string
    {
        return $this->encryptedPath;
    }

    public function setEncryptedPath(string $encryptedPath): static
    {
        $this->encryptedPath = $encryptedPath;

        return $this;
    }

    public function getHardware(): ?Hardware
    {
        return $this->hardware;
    }

    public function setHardware(?Hardware $hardware): static
    {
        $this->hardware = $hardware;

        return $this;
    }
}
