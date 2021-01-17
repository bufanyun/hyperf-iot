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
     * @Message("微信短链接")
     */
    public const SHORTEN_LINK = 'shorten_link:';
    /**
     * @Message("选号哈希，缓存归属地号单60秒，防止客户重复请求")
     */
    public const SELECT_PHONES = 'select_phones:';
    /**
     * @Message("产品分类列表")
     */
    public const CLASSIFY_LIST = 'classify_list';
    /**
     * @Message("设备信息_*")
     */
    public const DEVICE = 'device_';

    /**
     * @Message("锁前缀")
     */
    public const LOCK = 'lock:';

    /**
     * @Message("掉线通知_*")
     */
    public const DROPNOTICE = 'dropnotice_';



}
