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
 * 返佣类型
 *
 */
class ProductCommissionCode extends AbstractConstants
{
    /**
     * @Message("激活在网")
     */
    public const TYPE_ACTIVATION = 1;
    /**
     * @Message("首次充值")
     */
    public const TYPE_FIRST_CHARGE = 2;
    /**
     * @Message("后续月返")
     */
    public const TYPE_MOON_RETURN = 3;
    /**
     * @Message("申请提现")
     */
    public const APPLY_FOR = 4;

}
