<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;


/**
 * @Constants
 * 产品订单状态码
 * PAY_STATUS_*  交易状态
 * PAY_TYPE_* 交易方式
 * STATUS_* 订单进度状态
 * ACTIVAT_STATUS_* 激活状态
 *
 */
class ProductOrderCode extends AbstractConstants
{

    /**
     * @Message("未付款")
     */
    public const PAY_STATUS_UNPAID = 0;

    /**
     * @Message("已支付")
     */
    public const PAY_STATUS_SUCCESSFUL = 1;

    /**
     * @Message("微信支付")
     */
    public const PAY_TYPE_WXPAY = 1;

    /**
     * @Message("支付宝支付")
     */
    public const PAY_TYPE_ALIPAY = 2;

    /**
     * @Message("QQ钱包")
     */
    public const PAY_TYPE_QQPAY = 3;

    /**
     * @Message("余额支付")
     */
    public const PAY_TYPE_BALANCE = 4;

    /**
     * @Message("审核中")
     */
    public const STATUS_TO_EXAMINE = 100;

    /**
     * @Message("待发货")
     */
    public const STATUS_TO_BE_DELIVERED = 101;

    /**
     * @Message("已发货")
     */
    public const STATUS_DELIVERED = 200;

    /**
     * @Message("已签收")
     */
    public const STATUS_SIGNED_IN = 300;

    /**
     * @Message("已取消/退货")
     */
    public const STATUS_RETURNED = 400;

    /**
     * @Message("审核失败")
     */
    public const STATUS_AUDIT_FAILED = 500;

    /**
     * @Message("待完善证件信息")
     */
    public const STATUS_TO_EVPI = 501;

    /**
     * @Message("请重新填写信息")
     */
    public const STATUS_REFILL_INFORMATION = 502;

    /**
     * @Message("订单终止")
     */
    public const STATUS_STOP = 600;

    /**
     * @Message("未激活")
     */
    public const ACTIVAT_STATUS_NOT = 0;

    /**
     * @Message("已激活")
     */
    public const ACTIVAT_STATUS_ALREADY = 1;

    /**
     * @Message("激活并充值")
     */
    public const ACTIVAT_STATUS_RECHARGED = 2;

}