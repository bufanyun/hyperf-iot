<?php
declare (strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Exception\BusinessException;
use App\Exception\DatabaseExceptionHandler;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Constants\StatusCode;
use App\Models\ProductCommissionCash;
use App\Models\User;
use App\Models\ProductCommissionLog;
use App\Constants\ProductCommissionCashCode;
use App\Constants\ProductCommissionCode;

/**
 * ProductCommissionCashController
 * 佣金提现
 * @package App\Controller\Admin
 *
 * @AutoController(prefix="admin_api/product_commission_cash")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class),
 * })
 *
 */
class ProductCommissionCashController extends BaseController
{

    use \Core\Common\Traits\Admin\Controller\Expert;

    /**
     *
     * @Inject()
     * @var ProductCommissionCash
     */
    private $model;

    /**
     *
     * @Inject()
     * @var User
     */
    private $userModel;

    /**
     *
     * @Inject()
     * @var ProductCommissionLog
     */
    private $productCommissionLogModel;


    /**
     * 申请提现
     * apply
     * author MengShuai <133814250@qq.com>
     * date 2021/01/19 09:23
     * @RequestMapping(path="apply")
     */
    public function apply()
    {
        $reqParam = $this->request->all();
        var_export($reqParam);
        $query    = $this->model->query();
        $currUser = $this->auth->check();
        $cash     = json_decode($currUser['cash'], true);
        if ($cash['alipay_name'] == "" || $cash['add_make_img'] == "" || $cash['alipay_account'] == "") {
            return $this->error(StatusCode::ERR_EXCEPTION_USER, '请先设置收款信息');
        }
        if ((int)$reqParam['commission'] < 1) {
            return $this->error(StatusCode::ERR_EXCEPTION, '请输入提现金额');
        }
        if ($currUser['commission'] < $reqParam['commission']) {
            return $this->error(StatusCode::ERR_EXCEPTION, '提现金额不能超过账户佣金');
        }
        if (!preg_match("/^[1-9][0-9]*$/", $reqParam['commission'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '提现金额必须是正整数');
        }

        $setting = $this->setting->getGroupConfig('cash');
        if ($setting['cash_price_min'] > $reqParam['commission']) {
            return $this->error(StatusCode::ERR_EXCEPTION, '提现金额不能低于' . $setting['cash_price_min']);
        }
        if ($query->where(['admin_id' => $currUser['id'], 'status' => ProductCommissionCashCode::STATUS_PROCESSING])->exists()) {
            return $this->error(StatusCode::ERR_EXCEPTION, '存在未处理完成的提现申请，请稍后再试');
        }

        $setting['cash_fee'] = !empty($setting['cash_fee']) ? $setting['cash_fee'] : 0;
        $reqParam['fee']     = sprintf("%.2f", $reqParam['money'] * ($setting['cash_fee'] / 100));
        //最低手续费
        $reqParam['fee'] = ($reqParam['fee'] < $setting['cash_fee_min']) ? $setting['cash_fee_min'] : $reqParam['fee'];
        //最高手续费
        $reqParam['fee'] = ($reqParam['fee'] > $setting['cash_fee_max']) ? $setting['cash_fee_max'] : $reqParam['fee'];
        //最终到账金额
        $reqParam['last_money'] = sprintf("%.2f", $reqParam['commission'] - $reqParam['fee']);
        $requestHeaders         = $this->request->getHeaders();
        Db::beginTransaction();
        try {
            $cashId = $query->insertGetId([
                'admin_id'   => $currUser['id'],
                'money'      => $reqParam['commission'],
                'fee'        => $reqParam['fee'],
                'last_money' => $reqParam['last_money'],
                'ip'         => getClientIp(),
                'useragent'  => $requestHeaders['user-agent'][0] ?? '',
                'status'     => ProductCommissionCashCode::STATUS_PROCESSING,
                'remarks'    => ProductCommissionCashCode::getMessage(ProductCommissionCashCode::STATUS_PROCESSING),
                'created_at' => date("Y-m-d H:i:s"),
            ]);

            $this->userModel->query()
                ->where(['id' => $currUser['id'], 'commission' => $currUser['commission']])
                ->decrement('commission', $reqParam['commission']);

            $this->productCommissionLogModel->query()->insert([
                'admin_id'        => $currUser['id'],
                'order_id'        => $cashId,
                'type'            => ProductCommissionCode::APPLY_FOR,
                'money'           => '-' . $reqParam['commission'],
                'before'          => $currUser['commission'],
                'after'           => $currUser['commission'] - $reqParam['commission'],
                'detailed_titile' => ProductCommissionCode::getMessage(ProductCommissionCode::APPLY_FOR),
                'created_at'      => date("Y-m-d H:i:s"),
            ]);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }

        return $this->success([], '申请成功，请等待系统处理！');
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
        $where    = []; //额外条件
        $query    = $this->model->query()->where($where);

        [$querys, $sort, $order, $offset, $limit] = $this->model->buildTableParams($reqParam, $query);

        $total = $querys
            ->orderBy($sort, $order)
            ->count();
        $list  = $querys
            ->orderBy($sort, $order)
            ->offset($offset)->limit($limit)
            ->get()
            ->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['status'] = ProductCommissionCashCode::getMessage($v['status']);
            }
            unset($v);
        }
        $currUser                 = $this->auth->check();
        $currUser['payment_date'] = date("Y-m-d H:i:s", time() + 86400 * 4);
        $result                   = ["total" => $total, "rows" => $list, 'user' => $currUser];
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