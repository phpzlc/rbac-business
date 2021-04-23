<?php

namespace App\Entity;

use App\Repository\UserAuthRoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAuthRoleRepository::class)
 * @ORM\Table(name="user_auth_role", options={"comment": "用户角色表"})
 */
class UserAuthRole
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="string")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="PHPZlc\PHPZlc\Doctrine\SortIdGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAuth")
     * @ORM\JoinColumn(name="user_auth_id", referencedColumnName="id")
     */
    private $userAuth;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

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
