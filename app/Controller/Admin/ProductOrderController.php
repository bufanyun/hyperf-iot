<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
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
use App\Models\ProductOrder;
use App\Constants\ProductOrderCode;

/**
 * ProductOrderController
 * 订单列表
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_order")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductOrderController extends BaseController
{

    /**
     *
     * @Inject()
     * @var ProductOrder
     */
    private $model;

    /**
     * 最近销售
     * lately
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="lately")
     */
    public function lately()
    {
        $admin = $this->auth->check();
        $list  = $this->model->query()
            ->with('product_sale:id,kind_name,name,icon')
            ->where(['product_order.admin_id' => $admin['id'], 'pay_status' => ProductOrderCode::PAY_STATUS_SUCCESSFUL])
            ->orderBy('product_order.id', 'desc')
            ->groupBy('product_order.sid')
            ->limit(10)
            ->get();
        $list  = $list ? $list->toArray() : [];
        return $this->success($list);
    }

    /**
     * list
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $query    = $this->model->query();

        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);
        $where = []; //额外条件

        $total = $querys
            ->with('product_sale:id,kind_name,name,icon')
            ->where($where)
            ->orderBy($sort, $order)
            ->count();
//        Db::enableQueryLog();
        $list = $querys
            ->with('product_sale:id,kind_name,name,icon')
            ->where($where)
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get();
//        var_export(Db::getQueryLog());

        $list = $list ? $list->toArray() : [];
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                //                $list[$k]['status'] = $v['status'] === 0 ? false : true;
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

    /**
     * switch
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="switch")
     */
    public function switch()
    {
        $reqParam = $this->request->all();
        if (!isset($reqParam['key'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的参数');
        }
        $primaryKey = $this->model->getKeyName();
        if (!isset($reqParam[$primaryKey])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的条件');
        }
        $query = $this->model->query();
        $where = [$primaryKey => $reqParam[$primaryKey]];
        $param = [
            'key'    => $reqParam['key'],
            'update' => isset($reqParam['update']) ? $reqParam['update'] : '',
        ];

        $update = $this->model->switch($where, $param, $query);
        return $this->success(['switch' => $update]);
    }


}