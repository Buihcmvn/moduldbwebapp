<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $area = null;

    #[ORM\Column]
    private ?int $hardware = null;

    #[ORM\Column]
    private ?int $software = null;

    #[ORM\Column]
    private ?int $developer = null;

    #[ORM\Column]
    private ?int $customer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(int $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getHardware(): ?int
    {
        return $this->hardware;
    }

    public function setHardware(int $hardware): static
    {
        $this->hardware = $hardware;

        return $this;
    }

    public function getSoftware(): ?int
    {
        return $this->software;
    }

    public function setSoftware(int $software): static
    {
        $this->software = $software;

        return $this;
    }

    public function getDeveloper(): ?int
    {
        return $this->developer;
    }

    public function setDeveloper(int $developer): static
    {
        $this->developer = $developer;

        return $this;
    }

    public function getCustomer(): ?int
    {
        return $this->customer;
    }

    public function setCustomer(int $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
