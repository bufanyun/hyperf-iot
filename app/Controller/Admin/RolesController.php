<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * RolesController.php
 *
 * User：YM
 * Date：2020/2/4
 * Time：下午9:41
 */


namespace App\Controller\Admin;

use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Throwable;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * RolesController
 * 角色控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/4
 * Time：下午9:41
 *
 * @Controller(prefix="admin_api/roles")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\RolesRepository $rolesRepo
 */
class RolesController extends BaseController
{
    /**
     * index
     * 角色列表，角色管理
     * User：YM
     * Date：2020/2/4
     * Time：下午9:54
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->rolesRepo->getRolesList($reqParam);

        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * store
     * 保存，新建、编辑都用该方法，区别是否有主键id
     * User：YM
     * Date：2020/2/4
     * Time：下午9:55
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        try {
            $reqParam = $this->request->all();
            $id = $this->rolesRepo->saveRoles($reqParam);

            return $this->success($id);
        } catch (Throwable $throwable) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,$throwable->getMessage());
        }
    }

    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/4
     * Time：下午9:56
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->rolesRepo->getInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除角色
     * User：YM
     * Date：2020/2/4
     * Time：下午9:56
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->rolesRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getPermissions
     * 获取绑定权限信息
     * User：YM
     * Date：2020/2/4
     * Time：下午9:56
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_permissions")
     */
    public function getPermissions()
    {
        $reqParam = $this->request->all();
        $data = $this->rolesRepo->getRolePermissions($reqParam['id']);

        return $this->success($data);
    }

    /**
     * savePermissions
     * 保存角色权限
     * User：YM
     * Date：2020/2/4
     * Time：下午9:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="save_permissions")
     */
    public function savePermissions()
    {
        $reqParam = $this->request->all();
        $this->rolesRepo->saveRolesPermissions($reqParam);

        return $this->success('ok');
    }

    /**
     * getUsers
     * User：YM
     * Date：2020/2/4
     * Time：下午9:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_users")
     */
    public function getUsers()
    {
        $reqParam = $this->request->all();
        $list = $this->rolesRepo->getUsers($reqParam);

        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * searchUser
     * 角色添加用户时搜索用户
     * User：YM
     * Date：2020/2/4
     * Time：下午9:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="search_user")
     */
    public function searchUser()
    {
        $reqParam = $this->request->all();
        $list = $this->rolesRepo->searchUser($reqParam);

        return $this->success($list);
    }

    /**
     * saveUser
     * 为角色添加成员
     * User：YM
     * Date：2020/2/4
     * Time：下午9:58
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="save_user")
     */
    public function saveUser()
    {
        $reqParam = $this->request->all();
        $this->rolesRepo->saveRolesUser($reqParam);

        return $this->success('ok');
    }

    /**
     * removeUser
     * 为角色移除用户
     * User：YM
     * Date：2020/2/4
     * Time：下午9:58
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="remove_user")
     */
    public function removeUser()
    {
        $reqParam = $this->request->all();
        $this->rolesRepo->removeRolesUser($reqParam);

        return $this->success('ok');
    }
}