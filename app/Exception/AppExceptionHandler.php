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

use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Core\Common\Container\Response;
use App\Constants\StatusCode;
use Core\Common\Facade\Log;
use Hyperf\HttpServer\Contract\RequestInterface;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @Inject
     * @var Response
     */
    protected $response;

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;


    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 异常信息处理
        $throwableMsg = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()).PHP_EOL.$throwable->getTraceAsString();

        // 获取日志name，
        if ( stripos($throwable->getFile(),'LoginAuthMiddleware.php') ) {
            $uri = $this->request->getRequestUri();
            $logName = str_replace('/','-',ltrim($uri,'/'));
        } else {
            $logName = requestEntry($throwable->getTrace());
        }
        // 获取日志实例
        $logger = Log::get($logName);

        // 判断是否由业务异常类抛出的异常
        if ($throwable instanceof BusinessException) {
            // 阻止异常冒泡
            $this->stopPropagation();
            // 业务逻辑错误日志处理
            $logger->warning($throwableMsg,getLogArguments());
            return $this->response->error($throwable->getCode(),$throwable->getMessage());
        }


        // 系统错误日志处理
        $logger->error($throwableMsg,getLogArguments());
        $msg = !empty($throwable->getMessage())?$throwable->getMessage():StatusCode::getMessage(StatusCode::ERR_SERVER);
        return $response->withAddedHeader('content-type', 'text/html; charset=utf-8')
            ->withStatus(StatusCode::ERR_SERVER)
            ->withBody(new SwooleStream($msg));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

}
