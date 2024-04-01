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
use PHPZlc\PHPZlc\Bundle\Safety\ActionLoad;
use Psr\Container\ContainerInterface;

class RBACFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        (new PermissionBusiness(ActionLoad::$globalContainer))->builtUpdatePermission();
    }

}