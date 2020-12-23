<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * LinkController.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午10:09
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
 * LinkController
 * 友情链接
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/10
 * Time：下午10:09
 *
 * @Controller(prefix="admin_api/link")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\LinkRepository $linkRepo
 */
class LinkController extends BaseController
{
    /**
     * index
     * 友情链接列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:20
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->linkRepo->getLinkList($reqParam);
        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * store
     * 友情链接保存
     * User：YM
     * Date：2020/2/10
     * Time：下午10:20
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->linkRepo->saveLink($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 获取友情链接详情
     * User：YM
     * Date：2020/2/10
     * Time：下午10:20
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->linkRepo->getInfo($reqParam['id']);
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除友情链接
     * User：YM
     * Date：2020/2/10
     * Time：下午10:21
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->linkRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * orderLink
     * 友情链接拖拽排序
     * User：YM
     * Date：2020/2/10
     * Time：下午10:21
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="order")
     */
    public function orderLink()
    {
        $reqParam = $this->request->all();
        $this->linkRepo->orderLink($reqParam['ids']);

        return $this->success('ok');
    }

    /**
     * typeList
     * 获取类型列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:30
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="type_list")
     */
    public function typeList()
    {
        $list = $this->linkRepo->typeList();
        return $this->success($list);
    }

}