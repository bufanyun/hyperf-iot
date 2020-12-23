<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * UserController.php
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午4:04
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * UserController
 * 用户管理
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午4:04
 *
 * @Controller(prefix="/admin_api/user")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\UserRepository $userRepo
 */
class UserController extends BaseController
{
    /**
     * index
     * 用户列表，用户管理
     * User：YM
     * Date：2020/2/5
     * Time：下午4:05
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->userRepo->getUserList(filterParams($reqParam));
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
     * Date：2020/2/5
     * Time：下午5:01
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->userRepo->saveUser($reqParam);

        return $this->success($id);
    }

    /**
     * Info
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="info")
     */
    public function Info()
    {
        return $this->success([
            'roles' => ['admin'],
            'introduction' => 'I am a super administrator',
            'avatar' =>   'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
            'name' =>  'Super Admin',
        ]);
    }


    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:25
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->userRepo->getInfo($reqParam['id']);
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除用户
     * User：YM
     * Date：2020/2/5
     * Time：下午4:26
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->userRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getRoles
     * 获取绑定角色信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:26
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_roles")
     */
    public function getRoles()
    {
        $data = $this->userRepo->getRolesList();

        return $this->success($data);
    }
}