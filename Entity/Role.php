<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ORM\Table(name="role", options={"comment": "角色表"})
 */
class Role
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="PHPZlc\PHPZlc\Doctrine\SortIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="角色标识不能为空")
     * @ORM\Column(name="tag", type="string", options={"comment":"角色平台内唯一标识"})
     */
    private $tag;

    /**
     * @Assert\NotBlank(message="角色名不能为空")
     * @ORM\Column(name="name", type="string", options={"comment":"角色名"})
     */
    private $name;

    /**
     *
     * @Assert\NotBlank(message="所属平台不能为空")
     * @ORM\Column(name="platform", type="string", options={"comment":"平台"})
     */
    private $platform;

    /**
     * @ORM\Column(name="group_name", type="string", nullable=true, options={"comment":"权限分组"})
     */
    private $groupName;

    /**
     * @ORM\Column(name="contain_role_ids", type="simple_array", nullable=true, options={"comment":"包含角色"})
     */
    private $containRoleIds;

    /**
     * @ORM\Column(name="permission_ids", type="simple_array", nullable=true, options={"comment":"角色权限"})
     */
    private $permissionIds;

    /**
     * @ORM\Column(name="is_built", type="boolean", options={"comment":"是否内置", "default":"0"})
     */
    private $isBuilt = false;

    /**
     * @Assert\NotBlank(message="数据版本不能为空")
     * @ORM\Column(name="data_version", type="string", options={"comment":"数据版本"})
     */
    private $dataVersion;

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
        return $this->isBuilt;
    }

    public function setIsBuilt(bool $isBuilt): self
    {
        $this->isBuilt = $isBuilt;

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