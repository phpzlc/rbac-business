<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PHPZlc\PHPZlc\Doctrine\SortIdGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: "role", options: ["comment" => "角色表"])]
class Role
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "string")]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: SortIdGenerator::class)]
    private ?string $id = null;

    #[Assert\NotBlank(message: "角色标识不能为空")]
    #[ORM\Column(type: "string", options: ["comment" => "角色平台内唯一标识"])]
    private ?string $tag = null;

    #[Assert\NotBlank(message: "角色名不能为空")]
    #[ORM\Column(type: "string", options: ["comment" => "角色名"])]
    private ?string $name = null;

    #[Assert\NotBlank(message: "所属平台不能为空")]
    #[ORM\Column(type: "string", options: ["comment" => "平台"])]
    private ?string $platform = null;

    #[ORM\Column(name: "group_name", type: "string", nullable: true, options: ["comment" => "权限分组"])]
    private ?string $groupName = null;

    #[ORM\Column(name: "contain_role_ids", type: "simple_array", nullable: true, options: ["comment" => "包含角色"])]
    private ?array $containRoleIds = [];

    #[ORM\Column(name: "permission_ids", type: "simple_array", nullable: true, options: ["comment" => "角色权限"])]
    private ?array $permissionIds = [];

    #[ORM\Column(name:"is_built", type: "smallint", options: ["comment" => "是否内置", "default" => 0])]
    private int $isBuilt = 0;

    #[Assert\NotBlank(message: "数据版本不能为空")]
    #[ORM\Column(type: "string", options: ["comment" => "数据版本"])]
    private ?string $dataVersion = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getContainRoleIds(): ?array
    {
        return $this->containRoleIds;
    }

    public function setContainRoleIds(?array $containRoleIds): self
    {
        $this->containRoleIds = $containRoleIds;

        return $this;
    }

    public function getPermissionIds(): ?array
    {
        return $this->permissionIds;
    }

    public function setPermissionIds(?array $permissionIds): self
    {
        $this->permissionIds = $permissionIds;

        return $this;
    }

    public function getIsBuilt(): ?bool
    {
        return (bool)$this->isBuilt;
    }

    public function setIsBuilt(bool $isBuilt): self
    {
        $this->isBuilt = (int)$isBuilt;

        return $this;
    }

    public function getDataVersion(): ?string
    {
        return $this->dataVersion;
    }

    public function setDataVersion(string $dataVersion): self
    {
        $this->dataVersion = $dataVersion;

        return $this;
    }
}
