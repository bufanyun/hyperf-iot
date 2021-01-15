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

namespace App\Exception;

use App\Constants\StatusCode;
use Hyperf\Server\Exception\ServerException;
use Hyperf\Utils\ApplicationContext;
use Throwable;

/**
 *
 * Class DatabaseExceptionHandler
 *
 * 数据库异常处理类
 *
 * @package App\Exception
 * author MengShuai <133814250@qq.com>
 * date 2021/01/15 09:41
 */
class DatabaseExceptionHandler extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = StatusCode::getMessage($code);
        }
        //记录日志
        $logger = ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get('DatabaseExceptionHandler','database');
        $logger->error($message, getLogArguments());

        //将错误打印到控制台
        var_export("数据库错误提示: \033[32m" .$message . "\r\n");

        //非开发模式，隐藏数据库报错信息
        if(env('APP_ENV','') !== 'dev'){
            $message = StatusCode::getMessage($code);
        }
        parent::__construct($message, $code, $previous);
    }
}
