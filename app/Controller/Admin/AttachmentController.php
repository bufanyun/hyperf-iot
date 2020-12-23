<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * AttachmentController.php
 *
 * User：YM
 * Date：2020/2/15
 * Time：下午3:50
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use App\Middleware\LoginAuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use App\Middleware\AdminAuthMiddleware;

/**
 * AttachmentController
 * 附件管理
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/15
 * Time：下午3:50
 *
 * @Controller(prefix="admin_api/attachment")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\AttachmentRepository $attachmentRepo
 */
class AttachmentController extends BaseController
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
        $list = $this->attachmentRepo->getAttachmentList($reqParam);
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
        $id = $this->attachmentRepo->saveAttachment($reqParam);

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
        $info = $this->attachmentRepo->getInfo($reqParam['id']);
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
        $this->attachmentRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }
}