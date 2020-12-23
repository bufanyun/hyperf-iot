<?php
/**
 * Created by PhpStorm.
 *​
 * Log.php
 *
 * 日志类
 *
 * User：YM
 * Date：2019/12/13
 * Time：下午5:18
 */


namespace Core\Common\Facade;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Logger\LoggerFactory;

/**
 * Log
 * 日志类
 * @package Core\Common\Facade
 * User：YM
 * Date：2019/12/13
 * Time：下午5:18
 */
class Log
{

    /**
     * get
     * 获取区分channel的日志实例
     * User：YM
     * Date：2019/12/13
     * Time：下午5:28
     * @param string $name
     * @return mixed
     */
    public static function get(string $name = 'hyperf')
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
    }
}