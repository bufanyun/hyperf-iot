<?php
//
//declare(strict_types=1);
///**
// * This file is part of Hyperf.
// *
// * @link     https://www.hyperf.io
// * @document https://hyperf.wiki
// * @contact  group@hyperf.io
// * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
// */
//return [
//    'default' => [
//        'driver' => Hyperf\Cache\Driver\RedisDriver::class,
//        'packer' => Hyperf\Utils\Packer\PhpSerializerPacker::class,
//        'prefix' => 'c:',
//    ],
//];


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
        //'driver' => Hyperf\Cache\Driver\RedisDriver::class,
        'driver' => Core\Common\Driver\CacheDriver::class,
        'packer' => Hyperf\Utils\Packer\PhpSerializerPacker::class,
        'pool' => 'cache', // 对应redis.php的配置使用
        'prefix' => 'c:',
    ],
];
