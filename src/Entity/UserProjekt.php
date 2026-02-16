<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\UserProjektRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProjektRepository::class)]
class UserProjekt
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $projekt_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getProjektId(): ?int
    {
        return $this->projekt_id;
    }

    public function setProjektId(int $projekt_id): static
    {
        $this->projekt_id = $projekt_id;
        return $this;
    }
}
