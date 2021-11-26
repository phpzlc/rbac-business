<?php
namespace App\Business\RBACBusiness;

use App\Entity\Permission;
use App\Entity\Role;
use Doctrine\DBAL\Connection;
use PHPZlc\PHPZlc\Abnormal\Errors;
use PHPZlc\PHPZlc\Bundle\Business\AbstractBusiness;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rule;

class PermissionBusiness extends AbstractBusiness
{
    /**
     * 内置权限书写位置
     *
     * 可以通过路由配置权限；
     * 在路由下方追加
    options:
        platform: platform
        permission_group: 权限分组
        permission_tag: 权限标识
        permission_description: 权限描述
     *
     * @return array[]
     */
    public function getBuiltPermissions()
    {
        return [
//            [
//                'platform' => '平台',
//                'permission_tag' => '权限标识',
//                'permission_description' => '权限描述',
//                'permission_group' => '权限分组',
//                'routes' => [],
//            ]
        ];
    }

    /**
     * 内置角色书写位置
     *
     * @return array[]
     */
    public function getBuiltRoles()
    {
        return [
//            [
//                'platform' => '平台',
//                'role_tag' => '角色标识',
//                'role_description' => '角色描述',
//                'role_group' => '角色分组',
//                'permission_tags' => [],
//                'contain_role_tags' => []
//            ]
        ];
    }

    public function validator($class): bool
    {
        if(parent::validator($class)){
            if($class instanceof Permission){
                $permission = $this->getDoctrine()->getRepository('App:Permission')->findAssoc([
                    'tag' => $class->getTag(),
                    'platform' => $class->getPlatform(),
                    'data_version' => $class->getDataVersion()
                ]);
            }

            if(!empty($permission)){
                if($class->getId() != $permission->getId()){
                    Errors::setErrorMessage('角色标识已存在'); return false;
                }
            }

            return true;
        }

        return false;
    }

    public function builtUpdatePermission()
    {
        //一个路由可以绑定到多个权限上去
        //一个路由只能绑定一个页面标识；页面本身的路由名就是其页面标识

        $data_version = time();
        $permissionRepository = $this->getDoctrine()->getRepository('App:Permission');

        /**
         * @var Connection $conn
         */
        $conn = $this->getDoctrine()->getConnection();

        $conn->beginTransaction();

        try {
            $permissions = [];

            //读取路由中的配置文件
            $routeCollection = $this->get('router')->getRouteCollection();

            foreach ($routeCollection->all() as $route_name => $route){
                $permission_tag = $route->hasOption('permission_tag') ? $route->getOption('permission_tag') : '';
                $permission_description = $route->hasOption('permission_description') ? $route->getOption('permission_description') : '';
                $permission_group = $route->hasOption('permission_group') ? $route->getOption('permission_group') : '';
                $platform = $route->hasOption('platform') ? $route->getOption('platform') : '';

                if(!empty($permission_tag) || !empty($permission_description) || !empty($permission_group)){
                    if(empty($platform)){
                        Errors::setErrorMessage('路由定义权限缺少 option platform to' . $route_name); return false;
                    }

                    if(empty($permission_tag)){
                        Errors::setErrorMessage('路由定义权限缺少 option permission_tag to' . $route_name); return false;
                    }

                    if(array_key_exists($platform, $permissions) && array_key_exists($permission_tag, $permissions[$platform])){
                        $permission = $permissions[$platform][$permission_tag];
                    }else{
                        $permission = $permissionRepository->findAssoc(['tag' => $permission_tag, 'platform' => $platform]);
                        if(empty($permission)){
                            $permission = new Permission();
                            $this->em->persist($permission);
                        }

                        $permission->setPlatform($platform);
                        $permission->setGroupName($permission_group);
                        $permission->setTag($permission_tag);
                        $permission->setDescription($permission_description);
                        $permission->setDataVersion($data_version);

                        $permissions[$platform][$permission_tag] = $permission;
                    }

                    $permission->addRoute($route_name);

                    if(!$this->validator($permission)){
                        return false;
                    }
                }

            }

            unset($permissions);

            //读取内置权限
            foreach ($this->getBuiltPermissions() as $builtPermission){
                $permission = $permissionRepository->findAssoc(['tag' => $builtPermission['permission_tag'], 'platform' => $builtPermission['platform']]);
                if(empty($permission)){
                    $permission = new Permission();
                    $this->em->persist($permission);
                }

                $permission->setPlatform($builtPermission['platform']);
                $permission->setGroupName($builtPermission['permission_group']);
                $permission->setTag($builtPermission['permission_tag']);
                $permission->setDescription($builtPermission['permission_description']);
                $permission->setDataVersion($data_version);
                $permission->setRoutes($builtPermission['routes']);

                if(!$this->validator($permission)){
                    return false;
                }

            }

            $this->em->flush();
            $this->em->clear();


            //查询数据库中版本值不对的规则进行删除
            $del_permissions = $permissionRepository->findAll([
                'dataVersion' . Rule::RA_CONTRAST => ['<>', $data_version]
            ]);

            $roleRepository = $this->getDoctrine()->getRepository('App:Role');
            $roleBusiness = new RoleBusiness($this->container);

            foreach ($del_permissions as $del_permission){
                //获取该权限在哪些角色中使用;如果有使用情况就移除
                $roles = $roleRepository->findAll(['permission_ids' . Rule::RA_LIKE => $del_permission->getId()]);
                foreach ($roles as $role){
                    $roleBusiness->removePermission($role, $del_permission, false);
                }

                $this->em->remove($del_permission);
            }

            //内置角色
            foreach ($this->getBuiltRoles() as $builtRole){
                $role = new Role();
                $role
                    ->setPlatform($builtRole['platform'])
                    ->setTag($builtRole['role_tag'])
                    ->setName($builtRole['role_description'])
                    ->setGroupName($builtRole['role_group'])
                    ->setDataVersion($data_version)
                    ->setIsBuilt(true);
                
                $permission_ids = [];
                foreach ($builtRole['permission_tags'] as $permission_tag){
                    $permission_ids[] = $permissionRepository->findAssoc(['tag' => $permission_tag, 'platform' => $role->getPlatform()])->getId();
                }
                $role->setPermissionIds($permission_ids);

                $contain_role_ids = [];
                foreach ($builtRole['contain_role_tags'] as $contain_role_tag){
                    $contain_role_ids[] = $roleRepository->findAssoc(['tag' => $contain_role_tag, 'platform' => $role->getPlatform()])->getId();
                }
                $role->setContainRoleIds($contain_role_ids);

                $roleBusiness->create($role, false);
                Errors::clearError();
            }

            $this->em->flush();
            $this->em->clear();

            $conn->commit();

            return true;

        } catch (\Exception $exception){
            $conn->rollBack();
            $this->networkError($exception);
            return false;
        }
    }
}
