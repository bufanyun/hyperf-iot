<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * PermissionsService.php
 *
 * User：YM
 * Date：2020/2/3
 * Time：下午4:08
 */


namespace Core\Services;


/**
 * PermissionsService
 * 权限服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/3
 * Time：下午4:08
 *
 * @property \App\Models\SystemPermission $systemPermissionModel
 */
class PermissionsService extends BaseService
{

    public function getFillable()
    {
        return $this->systemPermissionModel->getFillable();
    }


    /**
     * getList
     * 条件获取权限列表
     * User：YM
     * Date：2020/2/3
     * Time：下午4:23
     * @param array $where
     * @param array $order
     * @return array
     */
    public function getList($where = [], $order = ['order' => 'ASC'])
    {
        $where = array_intersect($this->getFillable(),$where);

        $list = $this->systemPermissionModel->getList($where, $order);

        return $list;
    }

    /**
     * getPermissionsTreeList
     * 获取树形结构的权限列表
     * User：YM
     * Date：2020/2/3
     * Time：下午4:24
     * @return array
     */
    public function getPermissionsTreeList()
    {
        $list = $this->getList();
        foreach ($list as &$v) {
            $v['effect_uri_alias'] = $v['effect_uri'] && mb_strlen($v['effect_uri']) > 24?mb_substr($v['effect_uri'],0,24).'...':'';
        }
        unset($v);

        $tree = handleTreeList($list);

        return $tree;
    }

    /**
     * getParentIds
     *  获取父级们的id，组成数组
     * User：YM
     * Date：2020/2/3
     * Time：下午5:08
     * @param $id
     * @return array
     */
    public function getParentIds($id)
    {
        $arr = [];
        $info = $this->systemPermissionModel->getInfo($id);
        if ($info && $info['parent_id']) {
            $arr[] = $info['parent_id'];
            $arr = array_merge($this->getParentIds($info['parent_id']),$arr);
        }

        return $arr;
    }


    /**
     * getPermissionsCount
     * 根据条件获取权限的总数
     * User：YM
     * Date：2020/2/4
     * Time：下午9:18
     * @param array $where
     * @return mixed
     */
    public function getPermissionsCount($where = [])
    {
        $count = $this->systemPermissionModel->getPermissionsCount($where);

        return $count;
    }

    /**
     * savePermissions
     * 保存权限，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/4
     * Time：下午9:13
     * @param $inputData
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function savePermissions($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }

        if (isset($inputData['parent_id'])){
            $saveData['parent_id'] = $inputData['parent_id'];
        }

        if (isset($inputData['display_name']) && $inputData['display_name']){
            $saveData['display_name'] = $inputData['display_name'];
        }

        if (isset($inputData['name'])){
            $saveData['name'] = $inputData['name'];
        }

        if (isset($inputData['effect_uri'])){
            $saveData['effect_uri'] = $inputData['effect_uri'];
        }

        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }

        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }

        $id = $this->systemPermissionModel->saveInfo($saveData);
        // 清除缓存
        clearPrefixCache('admin_user_permission');
        delCache('permissions-from-uri');
        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/3
     * Time：下午4:56
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->systemPermissionModel->getInfo($id);

        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/3
     * Time：下午7:34
     * @param $id
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteInfo($id)
    {
        $info = $this->systemPermissionModel->deleteInfo($id);
        // 清除缓存
        clearPrefixCache('admin_user_permission');
        delCache('permissions-from-uri');
        return $info;
    }

    /**
     * getPermissionsFromUri
     * 获取uri对应的权限标识
     * User：YM
     * Date：2020/3/4
     * Time：下午10:57
     * @return array|iterable
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPermissionsFromUri()
    {
        $c = getCache('permissions-from-uri');
        if ($c) {
          return $c;
        }
        $res = [];
        $list = $this->getList();
        foreach ($list as $v) {
            $tmp = $v['effect_uri'];
            if (!$tmp) {
                continue;
            }
            $tmp = explode(',',$tmp);
            foreach ($tmp as $v1) {
                if ( trim($v1) ) {
                    $res[trim($v1)][] = $v['name'];
                }
            }
        }
        setCache('permissions-from-uri',$res);
        return $res;
    }


}