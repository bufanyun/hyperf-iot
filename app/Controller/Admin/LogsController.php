<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Models\IpRegion;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\Log;

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

    use \Core\Common\Traits\Admin\Controller\Expert;

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

        [$total, $list] = $this->model->parallelSearch($reqParam, $where, $select);

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['city_id'] = $this->IpRegionModel->where(['id' => $v['city_id']])->value('name');
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }


    /**
     * 编辑/更新行
     * edit
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="edit")
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 21:24
     */
    public function edit()
    {
        if (!$this->request->has($this->model->getKeyName())) {
            return $this->error(StatusCode::ERR_EXCEPTION, '缺少编辑的条件');
        }
        $row = $this->model->query()
            ->where([$this->model->getKeyName() => $this->request->input($this->model->getKeyName()),])
            ->first();
        if (!$row) {
            return $this->error(StatusCode::ERR_EXCEPTION, '数据不存在');
        }

        if ($this->request->isMethod('post') && $this->model->edit($row, $this->request->all())) {
            return $this->success($row, '更新成功');
        }

        $row = $row->toArray();
        $row['city_id'] = $this->IpRegionModel->where(['id' => $row['city_id']])->value('name');
        return $this->success($row, '获取成功');
    }

}