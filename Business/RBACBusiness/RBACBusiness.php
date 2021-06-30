<?php

namespace App\Business\RBACBusiness;

use App\Business\AuthBusiness\CurAuthSubject;
use App\Entity\Role;
use App\Entity\UserAuth;
use App\Entity\UserAuthRole;
use PHPZlc\PHPZlc\Abnormal\PHPZlcException;
use PHPZlc\PHPZlc\Bundle\Business\AbstractBusiness;
use PHPZlc\PHPZlc\Doctrine\ORM\Rule\Rule;
use PHPZlc\PHPZlc\Doctrine\ORM\Untils\SQL;
use Psr\Container\ContainerInterface;

class RBACBusiness extends AbstractBusiness
{
    private $platform;

    private $isSuper = false;

    public function __construct(ContainerInterface $container, $platform = null)
    {
        parent::__construct($container);

        $this->platform = $platform;
    }

    public function setIsSuper($isSuper)
    {
        return $this->isSuper = $isSuper;
    }

    public function getIsSuper()
    {
        return $this->isSuper;
    }

    private function getUserAuth(UserAuth $userAuth = null)
    {
        if(empty($userAuth)){
            $userAuth = CurAuthSubject::getCurUserAuth();
        }

        if(empty($userAuth)){
            throw new PHPZlcException('不能对空用户鉴权');
        }

        return $userAuth;
    }


    public function getUserAllPermissions(UserAuth $userAuth = null, $refresh_cache = false)
    {
        $userAuth = $this->getUserAuth($userAuth);

        if(!$refresh_cache) {
            $cache = $this->get('session')->get($this->getCacheSessionName($userAuth));
            if (!empty($cache)) {
                return $cache;
            }
        }

        $roles = $this->getUserAllRoles($userAuth);

        $permissions = [];

        foreach ($roles as $role){
            if(!empty($role->getPermissionIds())) {
                $rolePermissions = $this->getDoctrine()->getRepository('App:Permission')->findAll([
                    'platform' => $this->platform,
                    'id' . Rule::RA_IN => SQL::in($role->getPermissionIds())
                ]);
                foreach ($rolePermissions as $permission){
                    if(!array_key_exists($permission->getTag(), $permissions)){
                        $permissions[$permission->getTag()] = 1;
                    }
                }
            }
        }

        $this->get('session')->set($this->getCacheSessionName($userAuth), $permissions);

        return $permissions;
    }

    public function getUserAllRoles(UserAuth $userAuth = null)
    {
        $userAuth = $this->getUserAuth($userAuth);

        $userRoleRepository = $this->getDoctrine()->getRepository('App:UserAuthRole');

        /**
         * @var UserAuthRole[] $userRoles
         */
        $userRoles = $userRoleRepository->findAll([
            'r.platform' => $this->platform,
            'user_auth_id' => $userAuth->getId(),
            'role' . Rule::RA_JOIN => array(
                'alias' => 'r'
            ),
            Rule::R_SELECT => 'sql_pre.*, r.id as r_id, r.tag as r_tag, r.containRoleIds, r.permission_ids'
        ]);

        $roles = [];

        foreach ($userRoles as $userRole) {
            $this->pushRoles($roles, $userRole->getRole());
        }

        return $roles;
    }

    private function pushRoles(&$roles, Role $role)
    {
        if(!array_key_exists($role->getId(), $roles)){
            $roles[$role->getTag()] = $role;
            if(!empty($role->getContainRoleIds())){
                $containRoles = $this->getDoctrine()->getRepository('App:Role')->findAll(['platform' => $this->platform, 'id' . Rule::RA_IN => SQL::in($role->getContainRoleIds())]);
                foreach ($containRoles as $containRole){
                    $this->pushRoles($roles, $containRole);
                }
            }
        }
    }

    public function canRoute($route, UserAuth $userAuth = null)
    {
        if($this->getIsSuper()){
            return true;
        }

        $userAuth = $this->getUserAuth($userAuth);

        $route = $this->get('router')->getRouteCollection()->get($route);

        if(empty($route)){
            return true;
        }

        if(!$route->hasOption('permission_tag')){
            return true;
        }else{
            return $this->can($route->getOption('permission_tag'),'and', $userAuth);
        }
    }

    public function can($permissions, $model = 'and', UserAuth $userAuth = null)
    {
        if($this->getIsSuper()){
            return true;
        }

        $userAuth = $this->getUserAuth($userAuth);

        if(!in_array($model, ['and', 'or'])){
            throw new PHPZlcException('鉴权模式溢出, or 或者 and');
        }

        $userPermissions = $this->getUserAllPermissions($userAuth);

        if(is_array($permissions)){
            foreach ($permissions as $permission){
                if(array_key_exists($permission, $userPermissions)) {
                    if($model == 'or'){
                        return true;
                    }
                }else{
                    if($model == 'and'){
                        return false;
                    }
                }
            }
        }else{
            return array_key_exists($permissions, $userPermissions);
        }

        if($model == 'or'){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 得到权限缓存session名称
     *
     * @param UserAuth|null $userAuth
     * @return string
     */
    public function getCacheSessionName(UserAuth $userAuth = null)
    {
        $userAuth = $this->getUserAuth($userAuth);

        return 'hasPermissionCache' . $userAuth->getId() . $this->platform;
    }

    /**
     * 得到权限菜单缓存session名称
     */
    public function getCacheMenusSessionName(UserAuth $userAuth = null)
    {
        $userAuth = $this->getUserAuth($userAuth);

        return 'hasPermissionMenusCache' . $userAuth->getId() . $this->platform;
    }

    /**
     * 清除缓存
     *
     * @param UserAuth|null $userAuth
     */
    public function clearCache(UserAuth $userAuth = null)
    {
        $this->get('session')->remove($this->getCacheSessionName($userAuth));
        $this->get('session')->remove($this->getCacheMenusSessionName($userAuth));
    }

    /**
     * 菜单过滤
     *
     * @param $menus
     * @param UserAuth|null $userAuth
     * @param false $refresh_cache
     * @return array
     */
    public function menusFilter($menus, UserAuth $userAuth = null, $refresh_cache = false)
    {
        $userAuth = $this->getUserAuth($userAuth);

        if(!$refresh_cache) {
            $cache = $this->get('session')->get($this->getCacheMenusSessionName($userAuth));
            if (!empty($cache)) {
                return $cache;
            }
        }

        $menus = $this->menusFilterExec($menus, $userAuth);

        $this->get('session')->set($this->getCacheMenusSessionName($userAuth), $menus);

        return $menus;
    }

    /**
     * 菜单过滤执行
     *
     * @param $menus
     * @param UserAuth|null $userAuth
     * @param bool $refresh_cache
     * @return array
     */
    public function menusFilterExec($menus, UserAuth $userAuth = null)
    {
        $userAuth = $this->getUserAuth($userAuth);

        foreach ($menus as $key => $menu) {
            $path = str_replace($this->get('request_stack')->getCurrentRequest()->getBaseUrl(), "", parse_url($menu->getUrl(),PHP_URL_PATH ));

            if(substr($path, 0, 1) == '/'){
                $route = $this->get('router')->match($path);
                if (!empty($route)) {
                    if (!$this->canRoute($route['_route'], $userAuth)) {
                        unset($menus[$key]);
                        continue;
                    }
                }
            }

            if(!empty($menu->getChilds())){
                $children = $this->menusFilterExec($menu->getChilds(), $userAuth);
                if(empty($children)){
                    unset($menus[$key]);
                }else{
                    $menu->setChilds($children);
                }
            }
        }

        $menus = array_merge($menus);

        return $menus;
    }
}
