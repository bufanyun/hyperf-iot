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

return [
    'default' => [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'database' => env('DB_DATABASE', 'hyperf'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8'),
        'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
        'prefix' => env('DB_PREFIX', ''),
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 50,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) env('DB_MAX_IDLE_TIME', 60),
        ],
        // 模型缓存配置
        'cache' => [
            //'handler' => Hyperf\ModelCache\Handler\RedisHandler::class,
            'handler' => Core\Common\Driver\ModelCacheDriver::class,
            'cache_key' => 'mc:%s:m:%s:%s:%s',
            'pool' => 'model_cache', // 这里确定使用redis.php，中的那个配置
            'prefix' => 'model_cache',
            'ttl' => 3600 * 24,
            'empty_model_ttl' => 600,
            'load_script' => true,
        ],
        'commands' => [
            'gen:model' => [
                'path' => 'app/Models',
                'uses' => '',
                'refresh_fillable' => true,
                'force_casts' => true,
                'inheritance' => 'BaseModel',
            ],
        ],
    ],
];
