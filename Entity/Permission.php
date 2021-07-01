<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use PHPZlc\PHPZlc\Abnormal\Errors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PermissionRepository::class)
 * @ORM\Table(name="permission", options={"comment":"权限表"})
 */
class Permission
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
     * @Assert\NotBlank(message="权限标识不能为空")
     * @ORM\Column(name="tag", type="string", options={"comment":"权限平台内唯一标识"})
     */
    private $tag;

    /**
     * @Assert\NotBlank(message="所属平台不能为空")
     * @ORM\Column(name="platform", type="string", options={"comment":"平台"})
     */
    private $platform;

    /**
     * @ORM\Column(name="group_name", type="string", nullable=true, options={"comment":"权限分组"})
     */
    private $groupName;

    /**
     *  @ORM\Column(name="description", type="string", options={"comment":"权限描述"})
     */
    private $description;

    /**
     * @ORM\Column(name="routes", type="array", options={"comment":"可以访问的路由"})
     */
    private $routes;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRoutes(): ?array
    {
        return $this->routes;
    }

    public function addRoute($route)
    {
        $this->routes[] = $route;

        return $this;
    }

    public function setRoutes(array $routes): self
    {
        $this->routes = $routes;

        return $this;
    }

    public function getDataVersion(): ?string
    {
        return $this->dataVersion;
    }

    public function setDataVersion(string $dataVersion): self
    {
        if($this->dataVersion == $dataVersion){
            Errors::setErrorMessage('数据版本无变化');

            return $this;
        }

        $this->dataVersion = $dataVersion;

        return $this;
    }

}
