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
use Hyperf\Session\Handler;

return [
    'handler' => Hyperf\Session\Handler\RedisHandler::class,
    'options' => [
        'connection' => 'session',
        'path' => BASE_PATH . '/runtime/session',
        'gc_maxlifetime' => 86400 * 7,   //有效时间
        'session_name' => 'HYPERF_SESSION_ID',
        'domain' => null,
    ],
];
