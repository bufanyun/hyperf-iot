<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * PermissionsRepository.php
 *
 * 文件描述
 *
 * User：YM
 * Date：2020/1/11
 * Time：下午2:44
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * PermissionsRepository
 * 类的介绍
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/1/11
 * Time：下午2:44
 * @property \Core\Common\Container\Auth $auth
 * @property \Core\Common\Container\AdminPermission $adminPermission
 * @property \Core\Services\PermissionsService $permissionsService
 */
class PermissionsRepository extends BaseRepository
{
    /**
     * getUserPermissionsList
     * 获取用户对应所有权限
     * User：YM
     * Date：2020/1/13
     * Time：下午4:43
     * @return array
     */
    public function getUserPermissionsList()
    {
        $userInfo = $this->auth->check();
        if (!isset($userInfo['id']) || !$userInfo['id']) {
            throw new BusinessException(StatusCode::ERR_NOT_LOGIN);
        }
        $userPermissions = $this->adminPermission->getUserAllPermissions($userInfo['id']);

        return $userPermissions;
    }

    /**
     * getPermissionsList
     * 后台获取权限
     * User：YM
     * Date：2020/2/4
     * Time：下午8:24
     * @return mixed
     */
    public function getPermissionsList()
    {
        $list = $this->permissionsService->getPermissionsTreeList();

        return $list;
    }

    /**
     * 获取权限树
     * getPermissionsTreeList
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/11/26 10:54
     */
    public function getPermissionsTreeList()
    {
        $list = $this->permissionsService->getList();
        $arrs = [];
        $list = recur($arrs, $list);
        return $list;
    }

    public function getFillable()
    {
        return $this->permissionsService->getFillable();
    }

    /**
     * 获取树
     * getList
     * @param $data
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/11/26 11:41
     */
    public function getList($data)
    {
        $list = $this->permissionsService->getList($data);
        return $list;
    }

    /**
     * savePermissions
     * 创建、编辑权限
     * User：YM
     * Date：2020/2/4
     * Time：下午9:07
     * @param $data
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function savePermissions($data)
    {
        if ( !(isset($data['id']) && $data['id']) ) {
            $data['order'] = $this->permissionsService->getPermissionsCount(['parent_id' => $data['parent_id']]);
        }

        $info = $this->permissionsService->savePermissions($data);
        return $info;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/4
     * Time：下午9:08
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->permissionsService->getInfo($id);

        return $info;
    }

    /**
     * orderPermissions
     * 拖拽排序
     * User：YM
     * Date：2020/2/4
     * Time：下午9:08
     * @param array $ids
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function orderPermissions($ids = [])
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
            $this->permissionsService->savePermissions($saveData);
            $order++;
        }

        return true;
    }

    /**
     * deleteInfo
     * 删除信息，存在子节点不允许删除
     * User：YM
     * Date：2020/2/4
     * Time：下午9:08
     * @param $id
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteInfo($id)
    {
        $count = $this->permissionsService->getPermissionsCount(['parent_id' => $id]);
        if ($count) {
            throw new Exception("存在子节点不允许删除！");
        }
        $info = $this->permissionsService->deleteInfo($id);
        return $info;
    }
}