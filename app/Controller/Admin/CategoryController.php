<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CategoryController.php
 *
 * User：YM
 * Date：2020/2/11
 * Time：下午4:54
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
 * CategoryController
 * 分类管理
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/11
 * Time：下午4:54
 *
 * @Controller(prefix="admin_api/category")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\CategoryRepository $categoryRepo
 */
class CategoryController extends BaseController
{
    /**
     * index
     * 分类列表
     * User：YM
     * Date：2020/2/11
     * Time：下午4:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $list = $this->categoryRepo->getCategoryList();

        $data = [
            'list' => $list,
        ];
        return $this->success($data);
    }

    /**
     * store
     * 保存分类
     * User：YM
     * Date：2020/2/11
     * Time：下午4:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->categoryRepo->saveCategory($reqParam);
        return $this->success($id);
    }

    /**
     * getInfo
     * 获取分类详情
     * User：YM
     * Date：2020/2/11
     * Time：下午4:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->categoryRepo->getCategoryInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];
        return $this->success($data);
    }

    /**
     * destroy
     * 删除分类
     * User：YM
     * Date：2020/2/11
     * Time：下午4:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->categoryRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * orderCategory
     * 分类拖拽排序
     * User：YM
     * Date：2020/2/11
     * Time：下午4:57
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="order")
     */
    public function orderCategory()
    {
        $reqParam = $this->request->all();
        $this->categoryRepo->orderCategory($reqParam['ids']);

        return $this->success('ok');
    }

}