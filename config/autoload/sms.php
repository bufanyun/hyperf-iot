<?php
/**
 * 配置文件
 *
 * @link     http://www.swoole.red
 * @contact  1712715552@qq.com
 */

use Hyperf\Guzzle\HandlerStackFactory;

return [
    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
//            'qcloud',
            'aliyun'
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => BASE_PATH . '/runtime/logs/easy-sms.log',
        ],
        'qcloud' => [
            'sdk_app_id' => '', // SDK APP ID
            'app_key' => '', // APP KEY
            'sign_name' => '', // 短信签名，如果使用默认签名，该字段可缺省（对应官方文档中的sign）
        ],
        'aliyun' => [
            'access_key_id' => env('ALIYUN_ACCESS_KEY_ID', ''),
            'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET', ''),
            'sign_name' => env('ALIYUN_SIGN_NAME', ''),
        ],
        //...
    ],
    'options' => [
        'config' => [
            'handler' => (new HandlerStackFactory())->create([
                'min_connections' => 1,
                'max_connections' => 30,
                'wait_timeout' => 3.0,
                'max_idle_time' => 60,
            ]),
        ],
    ]
];
