<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LecturerController.php
 *
 * User：YM
 * Date：2020/2/15
 * Time：上午11:24
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * LecturerController
 * 讲师控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/15
 * Time：上午11:24
 *
 * @Controller(prefix="admin_api/lecturer")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\LecturerRepository $lecturerRepo
 */
class LecturerController extends BaseController
{
    /**
     * index
     * 讲师列表
     * User：YM
     * Date：2020/2/15
     * Time：上午11:36
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->lecturerRepo->getLecturerList($reqParam);

        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * store
     * 讲师保存
     * User：YM
     * Date：2020/2/15
     * Time：上午11:37
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->lecturerRepo->saveLecturer($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 获取讲师详情
     * User：YM
     * Date：2020/2/15
     * Time：上午11:37
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->lecturerRepo->getInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除讲师
     * User：YM
     * Date：2020/2/15
     * Time：上午11:38
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->lecturerRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * searchUser
     * 搜索用户
     * User：YM
     * Date：2020/2/15
     * Time：上午11:38
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="search_user")
     */
    public function searchUser()
    {
        $reqParam = $this->request->all();
        $list = $this->lecturerRepo->searchUser($reqParam);

        return $this->success($list);
    }
}