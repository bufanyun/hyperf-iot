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
use App\Constants\ProductCommissionCode;
use App\Models\ProductCommission;
use Core\Common\Extend\Helpers\ArrayHelpers;

/**
 * ProductCommissionController
 * 佣金规则
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_commission")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductCommissionController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var ProductCommission
     */
    private $model;

    /**
     * 多选下拉框
     * selecteds
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="selecteds")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function selecteds()
    {
        $query = $this->model->query();
        $where = []; //额外条件
        $list  = $query
            ->where($where)
            ->orderBy('type', 'ASC')
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]          = ArrayHelpers::hidden($v, ['id', 'admin_id', 'type', 'month', 'amount_money', 'money', 'detailed_titile', 'bind_products', 'created_at']);
                $list[$k]['value'] = $v['id'];
                switch ($v['type']) {
                    case 1 :
                        $list[$k]['brand'] = "[" . ProductCommissionCode::getMessage($v['type']) . "] 佣金：{$v['money']}";
                        break;
                    case 2 :
                        $list[$k]['brand'] = "[" . ProductCommissionCode::getMessage($v['type']) . "] 佣金：{$v['money']}-要求首充：{$v['amount_money']}";
                        break;
                    case 3 :
                        $list[$k]['brand'] = "[" . ProductCommissionCode::getMessage($v['type']) . "] 佣金：{$v['money']}-要求在网 {$v['month']} 月后";
                        break;
                    default :
                        $list[$k]['brand'] = '未知';
                        break;
                }
            }
            unset($v);
        }
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
            ->where($where)
            ->orderBy($sort, $order)
            ->count();
        //        Db::enableQueryLog();
        $list = $querys
            ->where($where)
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get();
        //        var_export(Db::getQueryLog());

        $list = $list ? $list->toArray() : [];
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['bind_products'] = (int)$v['bind_products'] + rand(1, 9);
                $list[$k]['type']          = ProductCommissionCode::getMessage($v['type']);
                switch ($v['type']) {
                    case 1:
                        $list[$k]['type_status'] = 'list-badge status-success';
                        break;
                    case 2:
                        $list[$k]['amount_money'] = '首充达' . $v['amount_money'] . '元';
                        $list[$k]['type_status']  = 'list-badge status-info';
                        break;
                    case 3:
                        $list[$k]['month']       = '在网达' . $v['month'] . '月';
                        $list[$k]['type_status'] = 'list-badge status-warning';
                        break;
                }
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

}