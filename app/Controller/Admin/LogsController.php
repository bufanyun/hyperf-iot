<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Core\Common\Extend\Helpers\ArrayHelpers;
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
use App\Models\Log;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Exception\ParallelExecutionException;

/**
 * LogsController
 * 操作日志
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/logs")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class LogsController extends BaseController
{

    /**
     *
     * @Inject()
     * @var Log
     */
    private $model;

    /**
     *
     * @Inject()
     * @var IpRegion
     */
    private $IpRegionModel;

    /**
     * list
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $select   = ['id', 'real_ip', 'city_id', 'url', 'uri', 'arguments', 'method', 'device', 'browser', 'execution_time', 'created_at', 'code', 'msg'];
        $where    = []; //额外条件

        [$total, $list] = parallel([
            function () use ($reqParam, $where, $select) {
                [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $this->model->query()->select($select)->where($where));
                return $querys->orderBy($sort, $order)->count();
            },
            function () use ($reqParam, $where, $select) {
                [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $this->model->query()->where($where)->select($select));
                return $querys->orderBy($sort, $order)->offset($offset)->limit($limit)->get()->toArray();
            },
        ]);

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['city_id'] = $this->IpRegionModel->where(['id' => $v['city_id']])->value('name');
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

}