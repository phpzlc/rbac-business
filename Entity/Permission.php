<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PHPZlc\PHPZlc\Abnormal\Errors;
use Symfony\Component\Validator\Constraints as Assert;
use PHPZlc\PHPZlc\Doctrine\SortIdGenerator;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Table(name: "permission", options: ["comment" => "权限表"])]
class Permission
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "string")]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: SortIdGenerator::class)]
    private ?string $id = null;

    #[Assert\NotBlank(message: "权限标识不能为空")]
    #[ORM\Column(type: "string", options: ["comment" => "权限平台内唯一标识"])]
    private ?string $tag = null;

    #[Assert\NotBlank(message: "所属平台不能为空")]
    #[ORM\Column(type: "string", options: ["comment" => "平台"])]
    private ?string $platform = null;

    #[ORM\Column(name: "group_name", type: "string", nullable: true, options: ["comment" => "权限分组"])]
    private ?string $groupName = null;

    #[ORM\Column(type: "string", options: ["comment" => "权限描述"])]
    private ?string $description = null;

    #[ORM\Column(type: "array", options: ["comment" => "可以访问的路由"])]
    private array $routes = [];

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
