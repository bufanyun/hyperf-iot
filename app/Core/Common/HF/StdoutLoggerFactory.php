<?php
/**
 * Created by PhpStorm.
 *​
 * StdoutLoggerFactory.php
 *
 * 日志输出工厂类
 *
 * User：YM
 * Date：2019/12/12
 * Time：下午7:17
 */


namespace Core\Common\HF;

use Core\Common\Facade\Log;

/**
 * StdoutLoggerFactory
 * 日志输出工厂类
 * @package Core\Common\Factory
 * User：YM
 * Date：2019/12/12
 * Time：下午7:17
 */
class StdoutLoggerFactory
{

    public function __invoke()
    {
        return Log::get('hyperf');
    }

}