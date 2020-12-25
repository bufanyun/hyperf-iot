<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\DbConnection\Db;


/**
 * 检查推广人身份信息
 * Class SpreadMiddleware
 * @package App\Middleware
 */
class SpreadMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    public function __construct(ContainerInterface $container, ServerRequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $all = $this->request->all();
        if (!isset($all['job_number'])) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '工号不能为空！');
        }
        $user = Db::table('user')->where(['job_number' => $all['job_number']])->first();
        if (!$user) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '工号不存在！');
        }
        if ($user->status !== 1) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '工号已被禁用！');
        }
        return $handler->handle($request);
    }
}