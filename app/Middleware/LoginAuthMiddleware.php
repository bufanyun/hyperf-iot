<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Exception\LoginException;
use Core\Common\Container\Auth;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * LoginAuthMiddleware
 * 验证是否登录了，没有登录用户进行拦截
 * @package App\Middleware
 * User：YM
 * Date：2020/3/1
 * Time：下午4:51
 */
class LoginAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var Auth
     */
    private $auth;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 判断是否为有效访问
        $token = getCookie('HYPERF_SESSION_ID');
        if ( is_null($token) ) {
            throw new LoginException(StatusCode::ERR_NOT_EXIST_TOKEN);
        }
        // 判断是否登录
        $currUser = $this->auth->check(false);
        if ($currUser === false) {
            throw new LoginException(StatusCode::ERR_NOT_LOGIN);
        }
        return $handler->handle($request);
    }
}