<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * MenuService.php
 *
 * User：YM
 * Date：2020/1/12
 * Time：上午8:28
 */


namespace Core\Services;


use Hyperf\Cache\Annotation\Cacheable;

/**
 * MenuService
 * 菜单服务
 * @package Core\Services
 * User：YM
 * Date：2020/1/12
 * Time：上午8:28
 *
 * @property \App\Models\SystemMenu $systemMenuModel
 * @property \Core\Common\Container\AdminPermission $adminPermission
 */
class MenuService extends BaseService
{
    /**
     * getList
     * 条件获取菜单列表
     * User：YM
     * Date：2020/1/13
     * Time：上午12:02
     * @param array $where
     * @param array $order
     * @return mixed
     */
    public function getList($where = [], $order = ['order' => 'ASC'])
    {

        $list = $this->systemMenuModel->getList($where, $order);

        return $list;
    }

    /**
     * getUserMenuList
     * 获取用户权限对应菜单列表
     * User：YM
     * Date：2020/1/12
     * Time：上午11:23
     * @param string $userId
     * @return array
     *
     * @Cacheable(prefix="admin_user_menu",ttl=9000, listener="admin-user-menu")
     */
    public function getUserMenuList($userId = '')
    {
        $userPermissions = $this->adminPermission->getUserAllPermissions($userId);
        $menuList = $this->getList();
        foreach ($menuList as $k => &$v) {
            if (!empty($v['url'])) {
                $v['url'] = '/'.ltrim($v['url'],'/');
            }
            if ( $v['system_permission_id'] && $v['permission_name'] && !in_array($v['permission_name'],$userPermissions) ) {
                unset($menuList[$k]);
            }
        }
        unset($v);

        $tree = handleTreeList($menuList);
        foreach ($tree as $k1 => $v1) {
            if ( !(isset($v1['children']) && $v1['children']) ) {
                unset($tree[$k1]);
            }
        }
        return $tree;
    }

    /**
     * getMenuTreeList
     * 获取树形结构的菜单列表
     * User：YM
     * Date：2020/2/3
     * Time：下午12:25
     * @return array
     */
    public function getMenuTreeList()
    {
        $list = $this->getList();
        foreach ($list as &$v) {
            if (!empty($v['url'])) {
                $v['url'] = '/'.ltrim($v['url'],'/');
            }
        }

        $tree = handleTreeList($list);

        return $tree;
    }

    /**
     * getMenuCount
     * 根据条件获取菜单的总数
     * User：YM
     * Date：2020/2/3
     * Time：下午4:52
     * @param array $where
     * @return mixed
     */
    public function getMenuCount($where = [])
    {
        $count = $this->systemMenuModel->getMenuCount($where);

        return $count;
    }

    /**
     * saveMenu
     * 保存菜单，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/3
     * Time：下午5:00
     * @param $inputData
     * @return null
     */
    public function saveMenu($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['parent_id'])){
            $saveData['parent_id'] = $inputData['parent_id'];
        }
        if (isset($inputData['system_permission_id'])){
            $saveData['system_permission_id'] = $inputData['system_permission_id'];
        }
        if (isset($inputData['display_name']) && $inputData['display_name']){
            $saveData['display_name'] = $inputData['display_name'];
        }
        if (isset($inputData['icon'])){
            $saveData['icon'] = $inputData['icon'];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['url'])){
            $saveData['url'] = $inputData['url'];
        }
        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional'];
        }
        $id = $this->systemMenuModel->saveInfo($saveData);
        // 清除缓存
        clearPrefixCache('admin_user_menu');
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
        $info = $this->systemMenuModel->getInfo($id);

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
     */
    public function deleteInfo($id)
    {
        $info = $this->systemMenuModel->deleteInfo($id);
        // 清除缓存
        clearPrefixCache('admin_user_menu');
        return $info;
    }
}