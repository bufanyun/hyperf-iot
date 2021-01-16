<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Core\Common\Traits\Admin\Table;
use Crypto\Rand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use http\Exception;
use App\Exception\BusinessException;
use Core\Plugins\BaiDu\Lbs;
use Hyperf\DbConnection\Db;
use App\Models\IpRegion;
use Hyperf\Utils\Parallel;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Constants\StatusCode;
use App\Models\ProductClassify;

/**
 * ProductClassifyController
 * 产品分类
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_classify")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductClassifyController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var ProductClassify
     */
    private $model;

    /**
     * 下拉框
     * selected
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="selected")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function selected()
    {
        $query = $this->model->query();
        $where = ['status' => 1]; //额外条件
        $list  = $query
            ->select('name', 'id as code')
            ->where($where)
            ->get()
            ->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                //    $list[$k]['status'] = $v['status'] === 0 ? false : true;
            }
            unset($v);
        }
        return $this->success($list);
    }
}