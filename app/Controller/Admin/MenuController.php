<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * MenuController.php
 *
 * User：YM
 * Date：2020/1/11
 * Time：下午1:58
 */


namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * MenuController
 * 菜单控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/1/11
 * Time：下午1:58
 *
 * @Controller(prefix="admin_api/menu")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 * @property \Core\Repositories\Admin\MenuRepository $menuRepo
 */
class MenuController extends BaseController
{
    /**
     * getUserMenu
     * 获取用户权限对应的菜单
     * User：YM
     * Date：2020/1/11
     * Time：下午2:47
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="user_menu")
     */
    public function getUserMenu()
    {
        $list = $this->menuRepo->getUserMenuList();

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * index
     * 函数的含义说明
     * User：YM
     * Date：2020/2/3
     * Time：下午12:20
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $list = $this->menuRepo->getMenuList();

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * getPermissions
     * 获取权限列表，创建菜单需要配置权限
     * User：YM
     * Date：2020/2/3
     * Time：下午4:04
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="permissions_list")
     */
    public function getPermissions()
    {
        $list = $this->menuRepo->getPermissionsList();

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * store
     * 菜单保存，新建、编辑都用该方法，区别是否有主键id
     * User：YM
     * Date：2020/2/3
     * Time：下午4:46
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->menuRepo->saveMenu($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/3
     * Time：下午4:47
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->menuRepo->getInfo($reqParam['id']);
        $pid = isset($info['system_permission_id']) ?$info['system_permission_id']:0;
        $menuPermissions = $this->menuRepo->getMenuPermissionList($pid);

        $data = [
            'info' => $info,
            'menu_permissions' => $menuPermissions,
        ];

        return $this->success($data);
    }

    /**
     * orderMenu
     * 菜单的拖拽排序
     * User：YM
     * Date：2020/2/3
     * Time：下午6:05
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="order")
     */
    public function orderMenu()
    {
        $reqParam = $this->request->all();

        $this->menuRepo->orderMenu($reqParam['ids']);

        return $this->success('ok');
    }

    /**
     * destroy
     * 删除菜单
     * User：YM
     * Date：2020/2/3
     * Time：下午6:06
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->menuRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }
}