<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * RolesService.php
 *
 * User：YM
 * Date：2020/2/4
 * Time：下午9:50
 */


namespace Core\Services;


/**
 * RolesService
 * 角色服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/4
 * Time：下午9:50
 *
 * @property \App\Models\SystemRole $systemRoleModel
 * @property \App\Models\SystemRolesUser $systemRolesUserModel
 * @property \App\Models\SystemRolesPermission $systemRolesPermissionModel
 */
class RolesService extends BaseService
{
    /**
     * getList
     * 条件获取角色列表
     * User：YM
     * Date：2020/2/4
     * Time：下午10:13
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {

        $list = $this->systemRoleModel->getList($where,$order,$offset,$limit);

        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:13
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->systemRoleModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveRoles
     * 保存角色，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/4
     * Time：下午10:57
     * @param $inputData
     * @return mixed
     */
    public function saveRoles($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }

        if (isset($inputData['display_name']) && $inputData['display_name']){
            $saveData['display_name'] = $inputData['display_name'];
        }

        if (isset($inputData['name'])){
            $saveData['name'] = $inputData['name'];
        }

        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }

        $id = $this->systemRoleModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:58
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->systemRoleModel->getInfo($id);

        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:58
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->systemRoleModel->deleteInfo($id);

        return $info;
    }

    /**
     * getRolePermissions
     * 获取角色的权限集合
     * User：YM
     * Date：2020/2/5
     * Time：上午11:07
     * @param $ids
     * @return array
     */
    public function getRolePermissions($ids)
    {
        $list = $this->systemRolesPermissionModel->getPermissionsByRolesIds($ids);
        $list = $this->handelPermissionsGroup($list);
        return $list;
    }

    /**
     * handelPermissionsGroup
     * 将角色权限列表，按着权限父级分组
     * User：YM
     * Date：2020/2/5
     * Time：上午11:05
     * @param array $list
     * @return array
     */
    public function handelPermissionsGroup($list = [])
    {
        if (!$list) {
            return [];
        }

        $tmp = [];
        foreach ($list as $v) {
            $tmp[$v['parent_id']][] = $v['system_permission_id'];
        }

        return $tmp;
    }

    /**
     * deleteRolesPermissions
     * 根据roleid删除对应的信息。
     * 由于角色权限一对多，所以每一次角色权限修改后会先做删除操作
     * User：YM
     * Date：2020/2/5
     * Time：上午11:12
     * @param $id
     * @return mixed
     */
    public function deleteRolesPermissions($id)
    {
        $info = $this->systemRolesPermissionModel->deleteRolesPermissions($id);

        return $info;
    }

    /**
     * saveRolesPermissions
     * 保存角色权限
     * User：YM
     * Date：2020/2/5
     * Time：上午11:13
     * @param array $data
     * @return mixed
     */
    public function saveRolesPermissions($data = [])
    {
        $info = $this->systemRolesPermissionModel->saveRolesPermissions($data);

        return $info;
    }

    /**
     * saveRolesUser
     * 保存用户对应角色
     * User：YM
     * Date：2020/2/5
     * Time：下午2:47
     * @param $data
     * @return mixed
     */
    public function saveRolesUser($data)
    {
        $info = $this->systemRolesUserModel->saveInfo($data);
        // 删除用户权限缓存
        flushAnnotationCache('admin-user-permission',$data['user_id']);
        // 删除用户菜单缓存
        flushAnnotationCache('admin-user-menu',$data['user_id']);
        return $info;
    }

    /**
     * saveUserRoles
     * 保存用户对应的角色，一对多
     * User：YM
     * Date：2020/2/5
     * Time：下午5:22
     * @param $data
     * @return mixed
     */
    public function saveUserRoles($data)
    {
        return $this->systemRolesUserModel->saveUserRoles($data);
    }

    /**
     * deleteRolesUser
     * 根据规则删除角色用户
     * User：YM
     * Date：2020/2/5
     * Time：下午2:41
     * @param $where
     * @return mixed
     */
    public function deleteRolesUser($where)
    {
        $info = $this->systemRolesUserModel->deleteRolesUser($where);
        // 删除用户权限缓存
        flushAnnotationCache('admin-user-permission',$where['user_id']);
        // 删除用户菜单缓存
        flushAnnotationCache('admin-user-menu',$where['user_id']);
        return $info;
    }

    /**
     * getRolesUserPagesInfo
     * 获取角色关联用户的分页信息
     * User：YM
     * Date：2020/2/5
     * Time：下午12:05
     * @param array $where
     * @return array
     */
    public function getRolesUserPagesInfo($where = [])
    {
        $pageInfo = $this->systemRolesUserModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * getRolesUserList
     * 获取角色关联用户的列表信息
     * User：YM
     * Date：2020/2/4
     * Time：下午11:31
     * @param array $where 条件
     * @param int $offset 偏移
     * @param int $limit 取值数量
     * @return mixed
     */
    public function getRolesUserList($where = [],$offset = 0, $limit = 0)
    {
        $list = $this->systemRolesUserModel->getList($where,$offset, $limit);

        return $list;
    }

    /**
     * getRoleUsers
     * 获取角色对应的用户的id集合
     * User：YM
     * Date：2020/2/4
     * Time：下午11:32
     * @param $roleId
     * @return array
     */
    public function getRoleUsers($roleId)
    {
        $where = ['system_role_id' => $roleId];

        $list = $this->getRolesUserList($where);
        $ids = array_pluck($list,'user_id');
        return $ids;
    }


    /**
     * getUserRoles
     * 获取用户对应的角色的id集合
     * User：YM
     * Date：2020/2/5
     * Time：下午4:35
     * @param $userId
     * @return array
     */
    public function getUserRoles($userId)
    {
        $where = ['user_id' => $userId];

        $list = $this->getRolesUserList($where);
        $ids = array_pluck($list,'system_role_id');
        return $ids;
    }
}