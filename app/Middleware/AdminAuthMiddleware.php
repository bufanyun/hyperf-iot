<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\AdminPermission;
use Core\Common\Container\Auth;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * AdminAuthMiddleware
 * 验证用户是否有路由请求权限
 * @package App\Middleware
 * User：YM
 * Date：2020/3/4
 * Time：下午11:01
 */
class AdminAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var RequestInterface
     */
    private $request;

    /**
     * @Inject()
     * @var Auth
     */
    private $auth;

    /**
     * @Inject()
     * @var AdminPermission
     */
    private $adminPermission;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uid = $this->auth->check(false);
//        var_export([$uid , config('super_admin')]);
        if ( $uid != config('super_admin') ) {
            $uri = $this->request->getRequestUri();
            $userPermissions = $this->adminPermission->getUserAllPermissions($uid);
            $uriPermissions = $this->adminPermission->getPermissionsFromUri($uri);
            if ( count(array_intersect($userPermissions,$uriPermissions)) == 0 ) {
                throw new BusinessException(StatusCode::ERR_NOT_ACCESS);
            }
        }
        return $handler->handle($request);
    }
}