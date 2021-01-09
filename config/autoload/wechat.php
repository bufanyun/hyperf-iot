<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

return [
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',
        // 日志
        'log' => [
            'default' => 'single',
            'channels' => [
                'single' => [
                    'driver' => env('WECHAT_LOG_DRIVER', 'errorlog'),
                    'level' => env('WECHAT_LOG_LEVEL', 'debug'),
                    'path' => env('WECHAT_LOG_DRIVER', BASE_PATH . '/runtime/logs/hyperf.log'),
                ],
            ],
        ],
    ],

    'official_account' => [
        'default' => [
            // AppID
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'your-app-id'),
            // AppSecret
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', 'your-app-secret'),
            // Token
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', 'your-token'),
            // EncodingAESKey
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', ''),
        ],
    ],
    //第三方开发平台
    //'open_platform'    => [
    //    'default' => [
    //        'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
    //        'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
    //        'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
    //        'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
    //    ],
    //],
    //小程序
    //'mini_program'     => [
    //    'default' => [
    //        'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
    //        'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
    //        'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
    //        'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
    //    ],
    //],
    //支付
    //'payment'          => [
    //    'default' => [
    //        'sandbox'    => env('WECHAT_PAYMENT_SANDBOX', false),
    //        'app_id'     => env('WECHAT_PAYMENT_APPID', ''),
    //        'mch_id'     => env('WECHAT_PAYMENT_MCH_ID', 'your-mch-id'),
    //        'key'        => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
    //        'cert_path'  => env('WECHAT_PAYMENT_CERT_PATH', 'path/to/cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
    //        'key_path'   => env('WECHAT_PAYMENT_KEY_PATH', 'path/to/cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
    //        'notify_url' => 'http://example.com/payments/wechat-notify',                           // 默认支付结果通知地址
    //    ],
    //    // ...
    //],
    //企业微信
    //'work'             => [
    //    'default' => [
    //        'corp_id'  => 'xxxxxxxxxxxxxxxxx',
    //        'agent_id' => 100020,
    //        'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
    //        //...
    //    ],
    //],
    //企业开放平台
    //'open_work'             => [
    //    'default' => [
    //        //参考EasyWechat官方文档
    //        //https://www.easywechat.com/docs/4.1/open-work/index
    //    ],
    //],
    //小微商户
    //'micro_merchant'   => [
    //    'default' => [
    //        //参考EasyWechat官方文档
    //        //https://www.easywechat.com/docs/4.1/micro-merchant/index
    //    ],
    //],
];
