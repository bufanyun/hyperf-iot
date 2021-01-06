<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\User;
use App\Models\ProductOrder;
use Hyperf\Di\Annotation\Inject;


/**
 * DashboardController
 * 控制台数据
 * @package App\Controller\Admin
 *
 * @Controller(prefix="/admin_api/dashboard")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \App\Models\User $userModel
 * @property \App\Models\ProductOrder $productOrderModel
 */
class DashboardController extends BaseController
{
    /**
     *
     * @Inject()
     * @var User
     */
    private $userModel;

    /**
     *
     * @Inject()
     * @var ProductOrder
     */
    private $productOrderModel;

    public function index()
    {

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
                //                $list[$k]['status'] = $v['status'] === 0 ? false : true;
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

    /**
     * Info
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="info")
     */
    public function info()
    {
        $currUser        = $this->auth->check();
        $permission_menu = [['test' => 6]];   //权限菜单
        return $this->success([
                'roles'           => ['admin'],
                'introduction'    => '千里号卡，正规卡推广系统，平台源码5000，提供渠道接口对接，有意者联系：15303830571',
                'name'            => $currUser['nickname'],
                'permission_menu' => $permission_menu,
            ] + $currUser);
    }

}