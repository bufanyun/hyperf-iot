<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * RolesRepository.php
 *
 * User：YM
 * Date：2020/2/4
 * Time：下午9:50
 */


namespace Core\Repositories\Admin;


use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;

/**
 * RolesRepository
 * 角色仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/4
 * Time：下午9:50
 *
 * @property \Core\Services\RolesService $rolesService
 * @property \Core\Services\PermissionsService $permissionsService
 * @property \Core\Services\UserService $userService
 */
class RolesRepository extends BaseRepository
{
    /**
     * getRolesList
     * 获取列表
     * User：YM
     * Date：2020/2/4
     * Time：下午10:02
     * @param $inputData
     * @return array
     */
    public function getRolesList($inputData)
    {
        $pagesInfo = $this->rolesService->getPagesInfo($inputData);

        $list = $this->rolesService->getList([],[],$pagesInfo['offset'],$pagesInfo['page_size']);

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }


    /**
     * saveRoles
     * 保存
     * User：YM
     * Date：2020/2/4
     * Time：下午10:03
     * @param $data
     * @return mixed
     */
    public function saveRoles($data)
    {
        return $this->rolesService->saveRoles($data);
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:03
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->rolesService->getInfo($id);

        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:03
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        // 删除用户组下对应用户的缓存权限，缓存菜单
        $ids = $this->rolesService->getRoleUsers($id);
        if ($ids) {
            flushAnnotationCache('admin-user-permission',$ids);
            flushAnnotationCache('admin-user-menu',$ids);
        }

        $info = $this->rolesService->deleteInfo($id);
        return $info;
    }

    /**
     * getAllPermissions
     * 获取所有权限treelist
     * User：YM
     * Date：2020/2/4
     * Time：下午10:05
     * @return array
     */
    public function getAllPermissions()
    {
        $list = $this->permissionsService->getPermissionsTreeList();

        return $list;
    }

    /**
     * getRolePermissions
     * 获取角色对应权限list
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $id 角色id
     * @return array
     */
    public function getRolePermissions($id)
    {
        if (!$id) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '参数不正确！');
        }
        $list = $this->rolesService->getRolePermissions($id);
        $allList = $this->getAllPermissions();
        foreach ($allList as $v) {
            if (!isset($list[$v['id']])) {
                $list[$v['id']] = [];
            }
        }

        $data = [
            'permissions_list' => $allList,
            'role_permissions' => $list,
        ];
        return $data;
    }

    /**
     * saveRolesPermissions
     * 保存角色权限
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $data
     * @return mixed
     */
    public function saveRolesPermissions($data)
    {
        $saveData = [];
        $roleId = $data['role_id'];
        $tmp = $data['permissions_id'];
        $time = date('Y-m-d H:i:s',time());

        $this->rolesService->deleteRolesPermissions($roleId);
        foreach ($tmp as $v) {
            foreach ($v as $v1) {
                $saveData[] = ['system_role_id' => $roleId,'system_permission_id' => $v1,'created_at' => $time,'updated_at' => $time];
            }
        }
        $status = $this->rolesService->saveRolesPermissions($saveData);
        // 删除用户组下对应用户的缓存权限，缓存菜单
        $ids = $this->rolesService->getRoleUsers($roleId);
        if ($ids) {
            flushAnnotationCache('admin-user-permission',$ids);
            flushAnnotationCache('admin-user-menu',$ids);
        }

        return $status;
    }

    /**
     * getUsers
     * 获取角色关联的用户list
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $inputData
     * @return array
     */
    public function getUsers($inputData)
    {
        if (isset($inputData['role_id'])) {
            $inputData['system_role_id'] = $inputData['role_id'];
            unset($inputData['role_id']);
        }
        $pagesInfo = $this->rolesService->getRolesUserPagesInfo($inputData);
        if (isset($inputData['page_size'])) {
            unset($inputData['page_size']);
        }
        if (isset($inputData['current_page'])) {
            unset($inputData['current_page']);
        }

        $list = $this->rolesService->getRolesUserList($inputData,$pagesInfo['offset'],$pagesInfo['page_size']);
        $list = $this->handleRolesUserList($list);
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * handleRolesUserList
     * 处理角色用户的列表
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param array $list
     * @return array
     */
    public function handleRolesUserList($list = [])
    {
        $tmp = [];
        foreach ($list as $v) {
            $tmp[] = $this->userService->getInfo($v['user_id']);
        }

        return $tmp;
    }

    /**
     * searchUser
     * 角色添加用户，用户搜索
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $inputData
     * @return array
     */
    public function searchUser($inputData)
    {
        $data = [];
        if (isset($inputData['search']) && $inputData['search']) {
            $ids = $this->rolesService->getRoleUsers($inputData['role_id']);
            $data = $this->userService->searchUserList($inputData['search'],[],$ids);
        }

        return $data;
    }

    /**
     * saveRolesUser
     * 为角色添加用户
     * User：YM
     * Date：2020/2/4
     * Time：下午10:07
     * @param $inputData
     * @return bool
     */
    public function saveRolesUser($inputData)
    {
        $saveData = [];
        if (isset($inputData['user_id']) && isset($inputData['role_id'])) {
            $saveData['system_role_id'] = $inputData['role_id'];
            $saveData['user_id'] = $inputData['user_id'];
            $this->rolesService->saveRolesUser($saveData);
        }

        return true;
    }

    /**
     * removeRolesUser
     * 为角色移除用户
     * User：YM
     * Date：2020/2/4
     * Time：下午10:07
     * @param $inputData
     * @return bool
     */
    public function removeRolesUser($inputData)
    {
        $where = [];
        if (isset($inputData['user_id']) && isset($inputData['role_id'])) {
            $where['system_role_id'] = $inputData['role_id'];
            $where['user_id'] = $inputData['user_id'];
            $this->rolesService->deleteRolesUser($where);
        } else {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '请求参数错误！');
        }

        return true;
    }
}