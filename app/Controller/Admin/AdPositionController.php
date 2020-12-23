<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *
 * AdPositionController.php
 *
 * User：YM
 * Date：2020/2/10
 * Time：下午4:57
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
 * AdPositionController
 * 轮播图
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/10
 * Time：下午4:57
 *
 * @Controller(prefix="admin_api/ad_position")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\AdPositionRepository $adPositionRepo
 */
class AdPositionController extends BaseController
{
    /**
     * index
     * 广告位列表
     * User：YM
     * Date：2020/2/10
     * Time：下午4:59
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->adPositionRepo->getAdPositionList($reqParam);
        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];
        return $this->success($data);
    }

    /**
     * store
     * 广告位保存
     * User：YM
     * Date：2020/2/10
     * Time：下午4:59
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->adPositionRepo->saveAdPosition($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 获取广告位详情
     * User：YM
     * Date：2020/2/10
     * Time：下午5:00
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->adPositionRepo->getInfo($reqParam['id']);
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除广告位
     * User：YM
     * Date：2020/2/10
     * Time：下午5:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->adPositionRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getVideoPreview
     * 获取视频预览信息
     * User：YM
     * Date：2020/2/10
     * Time：下午5:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_video_preview")
     */
    public function getVideoPreview()
    {
        $reqParam = $this->request->all();
        $info = $this->adPositionRepo->getVideoPreviewInfo($reqParam);

        return $this->success($info);
    }

    /**
     * typeList
     * 获取类型列表
     * User：YM
     * Date：2020/2/10
     * Time：下午5:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="type_list")
     */
    public function typeList()
    {
        $list = $this->adPositionRepo->typeList();

        return $this->success($list);
    }
}