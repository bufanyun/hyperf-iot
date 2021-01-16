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
use App\Models\ProductPenalty;
use App\Constants\ProductPenaltyCode;
use Core\Common\Extend\Helpers\ArrayHelpers;


/**
 * ProductPenaltyController
 * 禁区规则
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_penalty")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductPenaltyController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var ProductPenalty
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
        $query    = $this->model->query();
        $where = ['status' => 1]; //额外条件
        $list = $query
            ->where($where)
            ->orderBy('type', 'ASC')
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k] = ArrayHelpers::hidden($v, ['id', 'admin_id', 'type', 'province', 'city', 'district', 'status', 'created_at', 'updated_at', 'deleted_at']);
                $list[$k]['value'] = $v['id'];
               switch($v['type']){
                   case 1 :
                       $list[$k]['brand'] =  "[" . ProductPenaltyCode::getMessage($v['type']) . "] {$v['province']}";
                       break;
                   case 2 :
                       $list[$k]['brand'] =  "[" . ProductPenaltyCode::getMessage($v['type']) . "] {$v['province']}-{$v['city']}";
                       break;
                   case 3 :
                       $list[$k]['brand'] =  "[" . ProductPenaltyCode::getMessage($v['type']) . "] {$v['province']}-{$v['city']}-{$v['district']}";
                       break;
                   case 4 :
                       $list[$k]['brand'] =  "[" . ProductPenaltyCode::getMessage($v['type']) . "] {$v['province']}-{$v['city']}-{$v['district']}-{$v['district']}";
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
                        $list[$k]['type'] = '省级';
                        $list[$k]['type_status'] = 'list-badge status-success';
                        break;
                    case 2:
                        $list[$k]['type'] = '市级';
                        $list[$k]['type_status'] = 'list-badge status-info';
                        break;
                    case 3:
                        $list[$k]['type'] = '区域级';
                        $list[$k]['type_status'] = 'list-badge status-warning';
                        break;
                    case 4:
                        $list[$k]['type'] = '街道级';
                        $list[$k]['type_status'] = 'list-badge status-primary';
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


}