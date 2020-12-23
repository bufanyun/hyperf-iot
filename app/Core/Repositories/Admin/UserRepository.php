<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * UserRepository.php
 *
 * 文件描述
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午4:06
 */


namespace Core\Repositories\Admin;


use Core\Repositories\BaseRepository;

/**
 * UserRepository
 * 类的介绍
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午4:06
 *
 * @property \Core\Services\UserService $userService
 * @property \Core\Services\RolesService $rolesService
 *
 */
class UserRepository extends BaseRepository
{
    /**
     * getUserList
     * 获取列表
     * User：YM
     * Date：2020/2/5
     * Time：下午4:07
     * @param $inputData
     * @return array
     */
    public function getUserList($inputData)
    {
        $pagesInfo = $this->userService->getPagesInfo($inputData);
        $where = $inputData;
        unset($where['page_size']);
        unset($where['current_page']);
        $order = ['created_at'=>'DESC'];
        $list = $this->userService->getList($where,$order,$pagesInfo['offset'],$pagesInfo['page_size']);

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }

    /**
     * saveUser
     * 保存
     * User：YM
     * Date：2020/2/5
     * Time：下午4:52
     * @param $data
     * @return null
     * @throws \Exception
     */
    public function saveUser($data)
    {
        $saveRoles = [];
        $tmp = [];
        if (isset($data['user_roles'])) {
            $tmp = $data['user_roles'];
            unset($data['user_roles']);
        }
        // 判断是否是创建还是修改
        $type = true;
        if (isset($data['id']) && $data['id']) {
            $type = false;
        }
        $userId = $this->userService->saveUser($data,$type);
        $time = date('Y-m-d H:i:s',time());
        foreach ($tmp as $v) {
            $saveRoles[] = [
                'system_role_id' => $v,
                'user_id' => $userId,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }
        $this->rolesService->deleteRolesUser(['user_id' => $userId]);
        $this->rolesService->saveUserRoles($saveRoles);
        return $userId;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:28
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->userService->getInfo($id);
        $info['user_roles'] = $this->rolesService->getUserRoles($id);
        return $info;
    }

    /**
     * deleteInfo
     * 删除信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:28
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->userService->deleteInfo($id);
        $where = ['user_id' => $id];
        $this->rolesService->deleteRolesUser($where);
        return $info;
    }

    /**
     * getRolesList
     * 获取权限信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:28
     * @return mixed
     */
    public function getRolesList()
    {
        $list = $this->rolesService->getList();

        return $list;
    }

}