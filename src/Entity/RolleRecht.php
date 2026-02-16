<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\RolleRechtRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolleRechtRepository::class)]
class RolleRecht
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $rolle_id = null;

    #[ORM\Column]
    private ?int $recht_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getRolleId(): ?int
    {
        return $this->rolle_id;
    }

    public function setRolleId(int $rolle_id): static
    {
        $this->rolle_id = $rolle_id;
        return $this;
    }

    public function getRechtId(): ?int
    {
        return $this->recht_id;
    }

    public function setRechtId(int $recht_id): static
    {
        $this->recht_id = $recht_id;
        return $this;
    }
}
