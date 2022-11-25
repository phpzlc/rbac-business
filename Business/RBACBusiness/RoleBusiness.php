<?php

namespace App\Business\RBACBusiness;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\UserAuthRole;
use PHPZlc\PHPZlc\Abnormal\Errors;
use PHPZlc\PHPZlc\Bundle\Business\AbstractBusiness;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rule;

class RoleBusiness extends AbstractBusiness
{
    public function validator($class): bool
    {
        if(parent::validator($class)){
           if($class instanceof Role){
               $roleId = $this->getDoctrine()->getRepository(Role::class)->findAssoc([
                  'tag' => $class->getTag(),
                  'platform' => $class->getPlatform()
               ]);

               if(!empty($roleId) && $roleId->getId() != $class->getId()){
                   Errors::setErrorMessage('角色标识已存在'); return false;
               }

               $role = $this->getDoctrine()->getRepository(Role::class)->findAssoc([
                   'name' => $class->getName(),
                   'platform' => $class->getPlatform()
               ]);

               if(!empty($role) && $role->getId() != $class->getId()){
                   Errors::setErrorMessage('角色名称已存在'); return false;
               }
           }

           return true;
        }

        return false;
    }

    public function create(Role $role, $is_flush = true)
    {
        if(!$this->validator($role)){
            return false;
        }

        $this->em->persist($role);

        if($is_flush){
            $this->em->flush();
        }

        return true;
    }

    public function update(Role $role, $is_flush = true)
    {
        if(!$this->validator($role)){
            return false;
        }

        if($is_flush){
            $this->em->flush();
        }

        return true;
    }

    public function addContainRole(Role $role, Role $containRole, $is_flush = true)
    {
        if($containRole->getPlatform() != $role->getPlatform()){
            Errors::setErrorMessage('不同平台的权限不能公用'); return false;
        }

        $roleIds = $role->getContainRoleIds();
        if(in_array($containRole->getId(), $roleIds, true)){
            return true;
        }else{
            $roleIds[] = $containRole->getId();
        }

        $role->setContainRoleIds($roleIds);

        if($is_flush){
            $this->em->flush();
        }

        return true;
    }

    public function removeContainRole(Role $role, Role $containRole, $is_flush = true)
    {
        if($containRole->getPlatform() != $role->getPlatform()){
            Errors::setErrorMessage('不同平台的权限不能公用');
            return false;
        }

        $roleIds = $role->getContainRoleIds();
        if(in_array($containRole->getId(), $roleIds, true)){
            $roleIds = array_diff($roleIds, [$containRole->getId()]);
        }else{
            return true;
        }

        $role->setContainRoleIds($roleIds);

        if($is_flush) {
            $this->em->flush();
        }

        return true;
    }

    public function addPermission(Role $role, Permission $permission, $is_flush = true)
    {
        if($permission->getPlatform() != $role->getPlatform()){
            Errors::setErrorMessage('不同平台的权限不能公用');
            return false;
        }

        $permissionIds = $role->getPermissionIds();
        if(in_array($permission->getId(), $permissionIds, true)){
            return true;
        }else{
            $permissionIds[] = $permission->getId();
        }

        $role->setPermissionIds($permissionIds);

        if($is_flush) {
            $this->em->flush();
        }

        return true;
    }

    public function removePermission(Role $role, Permission $permission, $is_flush = true)
    {
        if($permission->getPlatform() != $role->getPlatform()){
            Errors::setErrorMessage('不同平台的权限不能公用');
            return false;
        }

        $permissionIds = $role->getPermissionIds();
        if(in_array($permission->getId(), $permissionIds, true)){
            $permissionIds = array_diff($permissionIds, [$permission->getId()]);
        }else{
            return true;
        }

        $role->setPermissionIds($permissionIds);

        if($is_flush) {
            $this->em->flush();
        }

        return true;
    }

    public function del(Role $role, $is_flush = true)
    {
        //获取该角色在哪些角色中使用；如果有使用情况就移除
        $useRoles = $this->getDoctrine()->getRepository(Role::class)->findAll(['contain_role_ids' . Rule::RA_LIKE => $role->getId()]);
        foreach ($useRoles as $useRole){
            $this->removeContainRole($useRole, $role, false);
        }

        //获取角色被哪些用户使用过
        $userRoles = $this->getDoctrine()->getRepository(UserAuthRole::class)->findAll(['role_id' => $role->getId()]);
        foreach ($userRoles as $userRole){
            $this->em->remove($userRole);
        }

        $this->em->remove($role);

        if($is_flush) {
            $this->em->flush();
        }

        return true;
    }
}