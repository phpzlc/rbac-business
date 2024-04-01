<?php

namespace App\Entity;

use App\Repository\UserAuthRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use PHPZlc\PHPZlc\Doctrine\SortIdGenerator;

#[ORM\Entity(repositoryClass: UserAuthRoleRepository::class)]
#[ORM\Table(name: "user_auth_role", options: ["comment" => "用户角色表"])]
class UserAuthRole
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "string")]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: SortIdGenerator::class)]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\UserAuth")]
    #[ORM\JoinColumn(name:"user_auth_id", referencedColumnName:"id")]
    private ?UserAuth $userAuth = null;

    #[ORM\ManyToOne(targetEntity:"App\Entity\Role")]
    #[ORM\JoinColumn(name:"role_id", referencedColumnName:"id")]
    private ?Role $role = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserAuth(): ?UserAuth
    {
        return $this->userAuth;
    }

    public function setUserAuth(?UserAuth $userAuth): self
    {
        $this->userAuth = $userAuth;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }


}
