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

namespace App\Exception\Handler;

use App\Constants\StatusCode;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Hyperf\HttpMessage\Exception\ServerErrorHttpException;
use Hyperf\HttpMessage\Exception\MethodNotAllowedHttpException;
use Hyperf\HttpMessage\Exception\BadRequestHttpException;
use Hyperf\HttpMessage\Exception\ForbiddenHttpException;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Exception\NotAcceptableHttpException;
use Hyperf\HttpMessage\Exception\RangeNotSatisfiableHttpException;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Hyperf\HttpMessage\Exception\UnprocessableEntityHttpException;
use Hyperf\HttpMessage\Exception\UnsupportedMediaTypeHttpException;
use Hyperf\Server\Exception\ServerException;
use App\Exception\BusinessException;
use App\Exception\LoginException;

class AppExceptionHandler extends ExceptionHandler
{

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(),
            $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        if ($throwable instanceof NotFoundHttpException) { //方法不存在
            $code = StatusCode::ERR_NON_EXISTENT;
        } elseif ($throwable instanceof BusinessException) { //常规错误
            $code = StatusCode::ERR_EXCEPTION_PARAMETER;
        } elseif ($throwable instanceof LoginException) {  //登录错误
            $code = StatusCode::ERR_NOT_LOGIN;

            //        }elseif ($throwable instanceof ServerErrorHttpException) {
            //            var_dump('ServerErrorHttpException');
            //        }elseif ($throwable instanceof MethodNotAllowedHttpException) {
            //            var_dump('MethodNotAllowedHttpException');
            //        }elseif ($throwable instanceof ServerException) {
            //            var_dump('ServerException');
            //        }elseif ($throwable instanceof BadRequestHttpException) {
            //            var_dump('BadRequestHttpException');
            //        }elseif ($throwable instanceof ForbiddenHttpException) {
            //            var_dump('ForbiddenHttpException');
            //        }elseif ($throwable instanceof HttpException) {
            //            var_dump('HttpException');
            //        }elseif ($throwable instanceof NotAcceptableHttpException) {
            //            var_dump('NotAcceptableHttpException');

            //        }elseif ($throwable instanceof RangeNotSatisfiableHttpException) {
            //            var_dump('RangeNotSatisfiableHttpException');
            //        }elseif ($throwable instanceof UnauthorizedHttpException) {
            //            var_dump('UnauthorizedHttpException');
            //        }elseif ($throwable instanceof UnprocessableEntityHttpException) {
            //            var_dump('UnprocessableEntityHttpException');
        } else {
            $code = 500;
        }

        if (StatusCode::ERR_NON_EXISTENT === $code) { //404文件不存在时，直接返回状态码
            $this->stopPropagation();
            return $response->withStatus(StatusCode::ERR_NON_EXISTENT);
        }

        $data = json_encode([
            'code' => $code,
            'msg'  => $throwable->getMessage(),
        ], JSON_UNESCAPED_UNICODE);
        return $response
            ->withHeader("Content-Type", "application/json;charset=utf-8")
            ->withStatus(200)
            ->withBody(new SwooleStream($data));
    }

    //    public function handle(Throwable $throwable, ResponseInterface $response)
    //    {
    //        // 判断被捕获到的异常是希望被捕获的异常
    //        if ($throwable instanceof Throwable) {
    //            // 格式化输出
    //            $data = json_encode([
    //                'code' => $throwable->getCode(),
    //                'message' => $throwable->getMessage(),
    //            ], JSON_UNESCAPED_UNICODE);
    //
    //            // 阻止异常冒泡
    ////            $this->stopPropagation();
    ////            var_dump($throwable->getTrace());
    //            return $response->withStatus(500)->withBody(new SwooleStream($data));
    //        }
    //
    //        // 交给下一个异常处理器
    //        return $response;
    //
    //        // 或者不做处理直接屏蔽异常
    //    }


    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

}
