<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LiveController.php
 *
 * User：YM
 * Date：2020/2/14
 * Time：下午11:21
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
 * LiveController
 * 直播管理
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/14
 * Time：下午11:21
 *
 * @Controller(prefix="admin_api/live")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\LiveRepository $liveRepo
 */
class LiveController extends BaseController
{
    /**
     * index
     * 直播列表
     * User：YM
     * Date：2020/2/14
     * Time：下午11:30
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->liveRepo->getLiveList($reqParam);

        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * store
     * 直播保存
     * User：YM
     * Date：2020/2/14
     * Time：下午11:31
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->liveRepo->saveLive($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 获取直播详情
     * User：YM
     * Date：2020/2/14
     * Time：下午11:32
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->liveRepo->getInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除直播
     * User：YM
     * Date：2020/2/14
     * Time：下午11:32
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->liveRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getStreamInfo
     * 获取直播流相关信息
     * User：YM
     * Date：2020/2/14
     * Time：下午11:33
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_stream_info")
     */
    public function getStreamInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->liveRepo->getStreamInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * searchLecturer
     * 直播搜索讲师
     * User：YM
     * Date：2020/2/14
     * Time：下午11:33
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="search_lecturer")
     */
    public function searchLecturer()
    {
        $reqParam = $this->request->all();
        $list = $this->liveRepo->searchLecturer($reqParam);

        return $this->success($list);
    }
}