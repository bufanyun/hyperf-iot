<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 后台用户充值枚举
 * @Constants
 */
class AdminMoneyRechargeCode extends AbstractConstants
{
    /**
     * @Message("支付宝")
     */
    public const PAYMENT_METHOD_ALIPAY = 'alipay';

    /**
     * @Message("微信支付")
     */
    public const PAYMENT_METHOD_WXPAY = 'wxpay';
    /**
     * @Message("未支付")
     */
    public const PAYMENT_STATUS_UNPAID = 1000;
    /**
     * @Message("支付中")
     */
    public const PAYMENT_STATUS_IN = 1001;
    /**
     * @Message("已支付")
     */
    public const PAYMENT_STATUS_PAID = 1002;

}
