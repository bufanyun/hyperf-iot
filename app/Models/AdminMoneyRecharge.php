<?php

declare (strict_types=1);

namespace App\Models;

use App\Constants\StatusCode;
use Hyperf\DbConnection\Db;
use App\Constants\AdminMoneyRechargeCode;
use App\Constants\AdminMoneyLogCode;
use App\Exception\DatabaseExceptionHandler;

/**
 * @property int            $id
 * @property string         $admin_id
 * @property string         $money
 * @property string         $orderid
 * @property string         $payid
 * @property string         $paytype
 * @property int            $paytime
 * @property string         $ip
 * @property string         $useragent
 * @property int            $status
 * @property \Carbon\Carbon $created_at
 */
class AdminMoneyRecharge extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_money_recharge';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'admin_id',
        'money',
        'orderid',
        'payid',
        'paytype',
        'paytime',
        'ip',
        'useragent',
        'status',
        'created_at',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'paytime' => 'integer', 'status' => 'integer', 'created_at' => 'datetime'];

    public const UPDATED_AT = null;

    protected $searchFields = ['admin_id', 'money', 'orderid', 'payid', 'ip'];

    /**
     * 充值回调处理
     * notifyHandle
     * @param AdminMoneyRecharge $orderInfo
     * @param array              $reqParam
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 21:49
     */
    public function notifyHandle(AdminMoneyRecharge $orderInfo, array $reqParam) : bool
    {
        Db::beginTransaction();
        try {
            $this->query()->where([
                'orderid' => $orderInfo->orderid,
                'status'  => AdminMoneyRechargeCode::PAYMENT_STATUS_UNPAID,
            ])->update([
                'payid'   => $reqParam['trade_no'],
                'paytime' => time(),
                'status'  => AdminMoneyRechargeCode::PAYMENT_STATUS_PAID,
            ]);
            $admin = User::query()->where(['id' => $orderInfo->admin_id])->first();
            User::query()->where(['id' => $orderInfo->admin_id])->increment('balance', $orderInfo->money);
            AdminMoneyLog::query()->insert([
                'admin_id'   => $orderInfo->admin_id,
                'money'      => $orderInfo->money,
                'before'     => $admin->balance,
                'after'      => $admin->balance + $orderInfo->money,
                'memo'       => AdminMoneyLogCode::MONEY_ONLINE_RECHARGE,
            ]);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            throw new DatabaseExceptionHandler(StatusCode::ERR_EXCEPTION_DATABASE, $ex->getMessage(), $ex);
        }

        return true;
    }

    /**
     * 创建唯一订单号
     * getOrderId
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 17:25
     */
    public function getOrderId()
    {
        $orderId = date("YmdHi") . str_rand(8);
        if ($this->query()->where(['orderid' => $orderId])->exists()) {
            return $this->getOrderId();
        }
        return $orderId;
    }
}