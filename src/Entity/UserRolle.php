<?php

namespace App\Entity;

use App\Entity\Traits\ArrayableTrait;
use App\Repository\UserRolleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRolleRepository::class)]
#[ORM\Table(name: 'user_rolle')]
class UserRolle
{
    use ArrayableTrait;
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRoles')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Rolle::class)]
    #[ORM\JoinColumn(name: 'rolle_id', referencedColumnName: 'id', nullable: false)]
    private $rolle;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRolle(): Rolle
    {
        return $this->rolle;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setRolle(Rolle $rolle): self
    {
        $this->rolle = $rolle;
        return $this;
    }
}

