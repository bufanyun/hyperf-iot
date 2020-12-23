<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * MenuRepository.php
 *
 * User：YM
 * Date：2020/1/11
 * Time：下午2:07
 */


namespace Core\Repositories\Admin;


use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;

/**
 * MenuRepository
 * 类的介绍
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/1/11
 * Time：下午2:07
 *
 * @property \Core\Common\Container\Auth $auth
 * @property \Core\Services\MenuService $menuService
 * @property \Core\Services\PermissionsService $permissionsService
 */
class MenuRepository extends BaseRepository
{
    /**
     * getUserMenuList
     * 获取用户权限可操作的菜单列表
     * User：YM
     * Date：2020/1/12
     * Time：上午8:27
     * @return array
     */
    public function getUserMenuList()
    {
        $userInfo = $this->auth->check();
        if (!isset($userInfo['id']) || !$userInfo['id']) {
            throw new BusinessException(StatusCode::ERR_NOT_LOGIN);
        }
        $list = $this->menuService->getUserMenuList($userInfo['id']);
        return $list;
    }

    /**
     * getMenuList
     * 后台获取菜单
     * User：YM
     * Date：2020/2/3
     * Time：下午12:23
     * @return mixed
     */
    public function getMenuList()
    {
        $list = $this->menuService->getMenuTreeList();

        return $list;
    }

    /**
     * getPermissionsList
     * 获取权限树结构list
     * User：YM
     * Date：2020/2/3
     * Time：下午4:06
     * @return mixed
     */
    public function getPermissionsList()
    {
        $list = $this->permissionsService->getPermissionsTreeList();

        return $list;
    }

    /**
     * saveMenu
     * 创建、编辑菜单
     * User：YM
     * Date：2020/2/3
     * Time：下午4:50
     * @param $data
     * @return mixed
     */
    public function saveMenu($data)
    {
        if ( !(isset($data['id']) && $data['id']) ) {
            $data['order'] = $this->menuService->getMenuCount(['parent_id' => $data['parent_id']]);
        }

        return $this->menuService->saveMenu($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/3
     * Time：下午4:55
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->menuService->getInfo($id);

        return $info;
    }

    /**
     * getMenuPermissionList
     * 通过菜单绑定权限id，获取该权限的权限树，转换成数组返回
     * User：YM
     * Date：2020/2/3
     * Time：下午5:03
     * @param $pid 绑定权限id
     * @return array
     */
    public function getMenuPermissionList($pid)
    {
        if (!$pid) {
            return [];
        }

        $arr = $this->permissionsService->getParentIds($pid);
        $arr[] = $pid;
        return $arr;
    }

    /**
     * orderMenu
     * 拖拽排序
     * User：YM
     * Date：2020/2/3
     * Time：下午7:31
     * @param array $ids
     * @return bool
     */
    public function orderMenu($ids = [])
    {
        if (count($ids) <= 1) {
            return true;
        }

        $order = 0; // 排序计数器
        foreach ($ids as $v) {
            $saveData = [
                'id' => $v,
                'order' => $order
            ];
            $this->menuService->saveMenu($saveData);
            $order++;
        }

        return true;
    }

    /**
     * deleteInfo
     * 删除信息，存在子节点不允许删除
     * User：YM
     * Date：2020/2/3
     * Time：下午7:32
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $count = $this->menuService->getMenuCount(['parent_id' => $id]);
        if ($count) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'存在子节点不允许删除');
        }

        $info = $this->menuService->deleteInfo($id);

        return $info;
    }
}