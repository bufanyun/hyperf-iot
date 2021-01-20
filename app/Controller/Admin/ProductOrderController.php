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
use App\Constants\StatusCode;
use App\Models\ProductOrder;
use App\Models\ProductOrderChannel;
use App\Constants\ProductOrderCode;

/**
 * ProductOrderController
 * 订单列表
 *
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
     * @Inject()
     * @var ProductOrderChannel
     */
    private $ProductOrderChannelModel;

    /**
     * 最近销售
     * lately
     *
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
     * 添加/插入行
     * add
     *
     * @return mixed
     *
     * @RequestMapping(path="add")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:45
     */
    public function add()
    {
        return $this->error(StatusCode::ERR_EXCEPTION, '访问非法');
    }

    /**
     * @return mixed
     *
     * @RequestMapping(path="del")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:45
     */
    public function del()
    {
        return $this->error(StatusCode::ERR_EXCEPTION, '访问非法');
    }

    /**
     * 获取订单进度状态码
     * get_status_selected
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="get_status_selected")
     */
    public function getStatusSelected()
    {
        return $this->success($this->model->getStatusSelected());
    }

    /**
     * 订单列表
     * list
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $query    = $this->model->query();

        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);
        $where = [];

        $total = $querys
            ->with('product_sale:id,kind_name,name,icon')
            ->where($where)
            ->orderBy($sort, $order)
            ->count();

        $list = $querys
            ->with('product_sale:id,kind_name,name,icon')
            ->where($where)
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get()
            ->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['status']         = ProductOrderCode::getMessage($v['status']);
                $list[$k]['activat_status'] = ProductOrderCode::getMessage($v['activat_status']);
                $list[$k]['sale_channel']   = $this->ProductOrderChannelModel->query()->where(['id' => $v['sale_channel']])->value('name') ?? '默认';
                $list[$k]['source']         = isset($v['source']) && $v['source'] !== '' ? $v['source'] : '互联网';

            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

}