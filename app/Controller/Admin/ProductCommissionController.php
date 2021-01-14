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
use App\Models\ProductCommission;

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
        if(!empty($list)){
            foreach ($list as $k => $v) {
                switch($v['type']){
                    case 1:
                        $list[$k]['type'] = '激活佣金';
                        $list[$k]['type_status'] = 'list-badge status-success';
                        break;
                    case 2:
                        $list[$k]['type'] = '首次充值';
                        $list[$k]['amount_money'] = '首充达'.$v['amount_money'].'元';
                        $list[$k]['type_status'] = 'list-badge status-info';
                        break;
                    case 3:
                        $list[$k]['type'] = '后续月返';
                        $list[$k]['month'] = '在网达'.$v['month'].'月';
                        $list[$k]['type_status'] = 'list-badge status-warning';
                        break;
                }
            }
            unset($v);
        }
        $result = array("total" => $total, "rows" => $list);
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
        if ( ! isset($reqParam['key'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少更新开关的参数');
        }
        $primaryKey = $this->model->getKeyName();
        if ( ! isset($reqParam[$primaryKey])) {
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

//    /**
//     * edit
//     * @return \Psr\Http\Message\ResponseInterface
//     *
//     * @RequestMapping(path="edit")
//     */
//    public function edit()
//    {
//        var_export('777');
//
//    }


}