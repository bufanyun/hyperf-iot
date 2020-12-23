<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * ArticleController.php
 *
 * User：YM
 * Date：2020/2/11
 * Time：下午8:55
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
 * ArticleController
 * 文章控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/11
 * Time：下午8:55
 *
 * @Controller(prefix="admin_api/article")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\ArticleRepository $articleRepo
 */
class ArticleController extends BaseController
{
    /**
     * index
     * 文章控制器列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->articleRepo->getList($reqParam);
        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * categoryList
     * 获取文章分类列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="category_list")
     */
    public function categoryList()
    {
        $list = $this->articleRepo->getCategoryList();
        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * categoryLabelList
     * 获取文章分类标签列表
     * User：YM
     * Date：2020/2/11
     * Time：下午9:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="category_label_list")
     */
    public function categoryLabelList()
    {
        $list = $this->articleRepo->getCategoryLabelList();

        $data = [
            'label_list' => $list
        ];

        return $this->success($data);
    }

    /**
     * store
     * 文章保存
     * User：YM
     * Date：2020/2/11
     * Time：下午9:01
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->articleRepo->saveArticle($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 获取文章信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->articleRepo->getInfo($reqParam['id']);
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除文章
     * User：YM
     * Date：2020/2/11
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->articleRepo->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getAttachment
     * 获取文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_attachment")
     */
    public function getAttachment()
    {
        $reqParam = $this->request->all();
        $list = $this->articleRepo->getAttachment($reqParam);

        return $this->success($list);
    }

    /**
     * saveAttachment
     * 保存文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="save_attachment")
     */
    public function saveAttachment()
    {
        $reqParam = $this->request->all();
        $id = $this->articleRepo->saveArticleAttachment($reqParam);

        return $this->success($id);
    }

    /**
     * deleteAttachment
     * 删除文章附件
     * User：YM
     * Date：2020/2/11
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete_attachment")
     */
    public function deleteAttachment()
    {
        $reqParam = $this->request->all();
        $this->articleRepo->deleteAttachmentInfo($reqParam['id']);

        return $this->success('ok');
    }
}