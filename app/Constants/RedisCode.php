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
 * @Constants
 */
class RedisCode extends AbstractConstants
{
    /**
     * @Message("选号哈希，缓存归属地号单60秒，防止客户重复请求")
     */
    const SELECT_PHONES = 'select_phones:';

    /**
     * @Message("设备信息_*")
     */
    const DEVICE = 'device_';

    /**
     * @Message("锁前缀")
     */
    const LOCK = 'lock:';

    /**
     * @Message("掉线通知_*")
     */
    const DROPNOTICE = 'dropnotice_';



}
