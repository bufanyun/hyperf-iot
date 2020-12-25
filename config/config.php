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
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

return [
    // 生产环境使用 prod 值
    'app_env' => env('APP_ENV', 'dev'),
    // 是否使用注解扫描缓存
    'scan_cacheable' => env('SCAN_CACHEABLE', false),
    'app_name' => env('APP_NAME', 'skeleton'),
    // 应用域名(静态资源访问使用)
    'app_domain' => env('APP_DOMAIN', ''),
    // 是否允许跨域资源访问
    'cors_access' => env('CORS_ACCESS', false),
    //用户id的前缀，作用在用户id的生成规则,最多使用3位
    'app_uid_prefix'=> env('APP_UID_PREFIX', 'iot'),
    'super_admin'=> env('SUPER_ADMIN', 'null'),
    // 允许跨域的域名
    'allow_origins' => [
        'http://127.0.0.1',
        'http://localhost',
        'http://www.yoctometer.com',
        'http://www.yinmengkeji.com',
        'http://www.hyperfcms.com',
        'http://api.iot.qvnidaye.com',
        'http://iot.qvnidaye.com',
    ],
    // 是否记录日志
    'app_log' => env('APP_LOG', false),
    // 是否记录框架的日志
    'hf_log' => env('HF_LOG', false),
    // 定义日志类型的输出
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
//            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
//            LogLevel::INFO,
//            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
];
