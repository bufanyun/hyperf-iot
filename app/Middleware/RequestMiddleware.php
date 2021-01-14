<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Utils\Context;

/**
 * 接到客户端请求，通过该中间件进行一些调整
 * Class RequestMiddleware
 *
 * @package App\Middleware
 * author MengShuai <133814250@qq.com>
 * date 2021/01/11 21:50
 */
class RequestMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    public function __construct(ContainerInterface $container,ServerRequestInterface $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // 为每一个请求增加一个qid
        $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) {
            $request = $request->withAddedHeader('qid', $this->getRequestId());
            //对参数进行安全过滤

            $params = $this->stripslashesRecursive($this->request->all());
            var_export(['$params' =>$params]);
            $request = $request->withQueryParams($params);
            return $request;
        });
        // 统一会话保持用session解决
        $tmp1 = getCookie('HYPERF_SESSION_ID');
        $tmp2 = $request->getHeader('HYPERF_SESSION_ID');
        $tmp3 = $request->getHeader('HYPERF-SESSION-ID');
        if (!$tmp1 && isset($tmp2[0]) && $tmp2[0]) {
            $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($tmp2) {
                $request = $request->withCookieParams(['HYPERF_SESSION_ID'=>$tmp2[0]]);
                return $request;
            });
        } elseif (!$tmp1 && isset($tmp3[0]) && $tmp3[0]) {
            $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($tmp3) {
                $request = $request->withCookieParams(['HYPERF_SESSION_ID'=>$tmp3[0]]);
                return $request;
            });
        }


        // 利用协程上下文存储请求开始的时间，用来计算程序执行时间
        Context::set('request_start_time',microtime(true));

        // http请求标志
        Context::set('http_request_flag',true);

        $response = $handler->handle($request);
        return $response;
    }

    /**
     * 唯一请求id
     * getRequestId
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 21:50
     */
    protected function getRequestId()
    {
        $tmp = $this->request->getServerParams();
        $name = strtoupper(substr(md5(gethostname()), 12, 8));
        $remote = strtoupper(substr(md5($tmp['remote_addr']),12,8));
        $ip = strtoupper(substr(md5(getServerLocalIp()), 14, 4));
        return uniqid(). '-' . $remote. '-'.$ip.'-'. $name;
    }

    protected function stripslashesRecursive(array $array) : array
    {
        foreach ($array as $k => $v) {
            if (is_string($v)){
                $array[$k] = addslashes(stripslashes(trim($v)));
            } elseif (is_array($v)){
                $array[$k] = $this->stripslashesRecursive($v);
            }
        }
        unset($v);
        var_export(['$array' =>$array]);
        return $array;
    }
}