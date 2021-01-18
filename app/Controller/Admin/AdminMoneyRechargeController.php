<?php

declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Exception\DatabaseExceptionHandler;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Exception\BusinessException;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Constants\StatusCode;
use App\Models\AdminMoneyRecharge;
use App\Constants\AdminMoneyRechargeCode;
use Core\Common\Extend\Epay\EpaySevice;

/**
 * AdminMoneyRechargeController
 * 余额充值表
 *
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/admin_money_recharge")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class AdminMoneyRechargeController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var AdminMoneyRecharge
     */
    private $model;

    /**
     * 跳转支付网关
     * jump
     *
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 16:13
     * @RequestMapping(path="jump")
     */
    public function jump()
    {
        $reqParam = $this->request->all();
        if (!isset($reqParam['orderid'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '订单号不能为空');
        }
        $result = $this->model->query()->where([
            'orderid' => $reqParam['orderid'],
            'status'  => AdminMoneyRechargeCode::PAYMENT_STATUS_UNPAID,
        ])->first();
        if (empty($result)) {
            return $this->error(StatusCode::ERR_EXCEPTION, '订单不存在或已处理');
        }
        $data = [
            'type'       => $result->paytype,
            'tradeno'    => $result->orderid,
            'price'      => $result->money,
            'return_url' => env('API_HOME_PAGE', '') . '/#/detailed/admin_money_recharge',
            'notify_url' => env('API_HOME_INTERFACE', '') . '/home/payment/admin_money_recharge_notify',
            'name'       => '余额充值',
            'sitename'   => '后台账户余额充值',
        ];
        $html = (make(EpaySevice::class))->purchase($data);
        return $this->view(['html' => $html]);
    }

    /**
     * 创建支付订单
     * create_order
     *
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 16:13
     * @RequestMapping(path="create_order")
     */
    public function create_order()
    {
        $reqParam = $this->request->all();
        if ($reqParam['money'] !== (int)$reqParam['money']) {
            return $this->error(StatusCode::ERR_EXCEPTION, '只能输入整数');
        }
        $reqParam['type'] = in_array($reqParam['type'], [
            AdminMoneyRechargeCode::PAYMENT_METHOD_ALIPAY,
            AdminMoneyRechargeCode::PAYMENT_METHOD_WXPAY,
        ]) ? $reqParam['type'] : AdminMoneyRechargeCode::PAYMENT_METHOD_ALIPAY;
        $requestHeaders   = $this->request->getHeaders();
        Db::beginTransaction();
        try {
            $orderId = $this->model->getOrderId();
            $res     = $this->model->query()->insert($this->model->loadModel([
                'admin_id'  => $this->auth->check(false),
                'money'     => $reqParam['money'],
                'orderid'   => $orderId,
                'paytype'   => $reqParam['type'],
                'ip'        => getClientIp(),
                'useragent' => $requestHeaders['user-agent'][0] ?? '',
                'status'    => AdminMoneyRechargeCode::PAYMENT_STATUS_UNPAID,
            ], null, false));
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }
        if (!$res) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,
                '创建订单失败，请稍后重试');
        }
        $data = [
            'pay_url' => env('API_HOME_INTERFACE', '') . '/admin_api/admin_money_recharge/jump?orderid=' . $orderId,
        ];
        return $this->success($data, '订单创建成功');
    }

    /**
     * list
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="list")
     */
    public function list()
    {
        $reqParam = $this->request->all();
        $where = []; //额外条件
        $query    = $this->model->query()->where($where);
        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);

        $total = $querys
            ->orderBy($sort, $order)
            ->count();

        $list = $querys
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get()
            ->toArray();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['status']  = AdminMoneyRechargeCode::getMessage($v['status']);
                $list[$k]['paytype'] = AdminMoneyRechargeCode::getMessage($v['paytype']);
                $list[$k]['paytime'] = isset($v['paytime']) ? date("Y-m-d H:i:s", $v['paytime']) : '-';
            }
            unset($v);
        }
        $result = ["total" => $total, "rows" => $list];
        return $this->success($result);
    }

}