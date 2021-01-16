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
 * 禁区类型
 *
 */
class ProductPenaltyCode extends AbstractConstants
{
    /**
     * @Message("省级")
     */
    public const TYPE_PROVINCE = 1;
    /**
     * @Message("市级")
     */
    public const TYPE_CITY = 2;
    /**
     * @Message("区域级")
     */
    public const TYPE_DISTRICT = 3;
    /**
     * @Message("街道级")
     */
    public const TYPE_ADDRESS = 4;
    
}
