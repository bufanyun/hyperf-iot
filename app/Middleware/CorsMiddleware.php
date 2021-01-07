<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Context;

/**
 * CorsMiddleware
 * 跨域中间件
 * 本中间件做了这样一件事，让跨域请求、非跨域请求、api请求，都是用默认的session保持回话，传统请求以外的请求
 * 通过header传输sessionid来保持会话，由于框架底层写死了HYPERF_SESSION_ID这个key，所以沿用……
 * 这样做必须满足两个条件：1、服务端开启cors_access允许跨域 2、客户端实现HYPERF_SESSION_ID的存储
 * 当然如果cookie有值，优先使用cookie值
 * @package App\Middleware
 * User：YM
 * Date：2020/1/3
 * Time：下午4:21
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    private $config;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 判断是否允许跨域请求，且处理跨域
        $corsAccess = $this->config->get('cors_access');
        if ($corsAccess === true) {
            $origins = $this->config->get('allow_origins');
            $origin = $request->getHeader('origin');
            $origin = $origin?$origin[0]:false;
            if ($origin != false) {
                // offset从5开始，避免http:引发问题
                $isPort = (int)strripos($origin,':',5);
                if ($isPort) {
                    $ifOrigin = in_array(substr($origin,0,$isPort),$origins);
                } else {
                    $ifOrigin = in_array($origin,$origins);
                }
                if ( $ifOrigin ) {
                    $response = Context::get(ResponseInterface::class);
                    $response = $response->withHeader('Access-Control-Allow-Origin', "{$origin}");
//                    $response = $response->withHeader('Access-Control-Allow-Origin', "*");
                    $response = $response->withHeader('Access-Control-Allow-Credentials', true);
                    $response = $response->withHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,hyperf-session-id');
                    Context::set(ResponseInterface::class, $response);
                    // 非简单跨域请求的"预检"请求处理
                    if ($request->getMethod() == 'OPTIONS') {
                        return $response;
                    }
                }
            }
        }

        $response = $handler->handle($request);
        return $response;
    }
}