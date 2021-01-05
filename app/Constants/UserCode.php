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
 * 用户类枚举
 * Class UserCode
 *
 * @Constants
 * @package App\Constants
 * author MengShuai <133814250@qq.com>
 * date 2021/01/05 13:55
 */
class UserCode extends AbstractConstants
{

    /**
     * @Message("合伙人")
     */
    public const LEVEL_PARTNER = 1001;

    /**
     * @Message("高级合伙人")
     */
    public const LEVEL_SENIOR_PARTNER = 1002;

    /**
     * @Message("营运商")
     */
    public const LEVEL_OPERATORS = 1003;

    public static function getLevelMap()
    {
        return [
            self::LEVEL_PARTNER => self::getMessage(self::LEVEL_PARTNER),
            self::LEVEL_SENIOR_PARTNER => self::getMessage(self::LEVEL_SENIOR_PARTNER),
            self::LEVEL_OPERATORS => self::getMessage(self::LEVEL_OPERATORS),
        ];
    }

    /**
     * @Message("未授权")
     */
    public const JOB_NUMBER_UNAUTHORIZED = 2000;

    /**
     * @Message("营运商")
     */
    public const JOB_NUMBER_AUTHORIZED = 2001;

    public static function getJobNumberMap()
    {
        return [
            self::JOB_NUMBER_UNAUTHORIZED => self::getMessage(self::JOB_NUMBER_UNAUTHORIZED),
            self::JOB_NUMBER_AUTHORIZED => self::getMessage(self::JOB_NUMBER_AUTHORIZED),
        ];
    }

    /**
     * @Message("已停用")
     */
    public const STATUS_STOP = 0;

    /**
     * @Message("已启用")
     */
    public const STATUS_ENABLE = 1;

    public static function getStatusMap()
    {
        return [
            self::STATUS_STOP => self::getMessage(self::STATUS_STOP),
            self::STATUS_ENABLE => self::getMessage(self::STATUS_ENABLE),
        ];
    }

}