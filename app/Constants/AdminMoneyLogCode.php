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
 * 明细备注规范
 * @Constants
 */
class AdminMoneyLogCode extends AbstractConstants
{
    /**
     * @Message("10001")
     */
    public const PURCHASE_LEVEL = '购买用户等级';
    /**
     * @Message("10002")
     */
    public const MONEY_ONLINE_RECHARGE = '余额在线充值';
}
