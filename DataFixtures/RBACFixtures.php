<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2020/11/9
 */

namespace App\DataFixtures;


use App\Business\RBACBusiness\PermissionBusiness;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;

class RBACFixtures extends Fixture
{
    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        (new PermissionBusiness($this->container))->builtUpdatePermission();
    }

}