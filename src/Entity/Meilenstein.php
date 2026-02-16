<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\MeilensteinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeilensteinRepository::class)]
class Meilenstein
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $projekt_id = null;

    #[ORM\Column]
    private ?int $software_id = null;

    #[ORM\Column]
    private ?int $hardware_id = null;

    #[ORM\Column(length: 255)]
    private ?string $entwickler = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $kommentar = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)] // Hoặc 'datetime' nếu bạn cần chỉnh sửa ngày sau khi tạo
    private ?\DateTimeImmutable $start = null; // Ngày bắt đầu dự kiến

    #[ORM\Column(type: 'datetime_immutable', nullable: true)] // Hoặc 'datetime'
    private ?\DateTimeImmutable $end = null;   // Ngày kết thúc dự kiến

    // Hoặc nếu bạn muốn theo dõi cả ngày thực tế:
    // #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    // private ?\DateTimeImmutable $actualStartDate = null;
    // #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    // private ?\DateTimeImmutable $actualEndDate = null;

    // --- Getters and Setters cho các thuộc tính mới ---
    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): static
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(\DateTimeImmutable $end): static
    {
        $this->end = $end;
        return $this;
    }

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

    public function getProjektId(): ?int
    {
        return $this->projekt_id;
    }

    public function setProjektId(int $projekt_id): static
    {
        $this->projekt_id = $projekt_id;

        return $this;
    }

    public function getSoftwareId(): ?int
    {
        return $this->software_id;
    }

    public function setSoftwareId(int $software_id): static
    {
        $this->software_id = $software_id;

        return $this;
    }

    public function getHardwareId(): ?int
    {
        return $this->hardware_id;
    }

    public function setHardwareId(int $hardware_id): static
    {
        $this->hardware_id = $hardware_id;

        return $this;
    }

    public function getEntwickler(): ?string
    {
        return $this->entwickler;
    }

    public function setEntwickler(string $entwickler): static
    {
        $this->entwickler = $entwickler;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getKommentar(): ?string
    {
        return $this->kommentar;
    }

    public function setKommentar(string $kommentar): static
    {
        $this->kommentar = $kommentar;

        return $this;
    }
}
