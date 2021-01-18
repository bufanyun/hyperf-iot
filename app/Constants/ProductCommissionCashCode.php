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
 * 佣金提现状态码
 *
 * @Constants
 */
class ProductCommissionCashCode extends AbstractConstants
{
    /**
     * @Message("正在处理")
     */
    public const STATUS_PROCESSING = 0;
    /**
     * @Message("已结算")
     */
    public const STATUS_SETTLED = 1;
    /**
     * @Message("交易冻结")
     */
    public const STATUS_FROZEN = 2;
    /**
     * @Message("拒绝打款")
     */
    public const STATUS_REJECT = 3;
}
