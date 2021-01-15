<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\ProductSale;
use Core\Common\Extend\Helpers\ArrayHelpers;

/**
 * ProductSaleController
 * 产品列表
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_sale")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductSaleController extends BaseController
{

    /**
     *
     * @Inject()
     * @var ProductSale
     */
    private $model;

    /**
     * list
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $query = $this->model->query();
        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);
        $where = []; //额外条件

        $total = $querys
            ->with('product_classify:id,name,icon')
            ->where($where)
            ->orderBy($sort, $order)
            ->count();
        //        Db::enableQueryLog();
        $list = $querys
            ->with('product_classify:id,name,icon')
            ->where($where)
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get()
            ->toArray();
        //        var_export(Db::getQueryLog());

        if(!empty($list)){
            foreach ($list as $k => $v) {
                $list[$k] = ArrayHelpers::hidden($v, ['first_desc', 'deleted_at', 'stocks', 'sales', 'penalty']);
                $list[$k]['pid_name']= $v['pid']>0 ? $this->model->getPidKindName($v['pid']) : $v['kind_name'];
            }
            unset($v);
        }
        $result = array("total" => $total, "rows" => $list);
        return $this->success($result);
    }

}