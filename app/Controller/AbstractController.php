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

namespace App\Controller;

use Core\Common\Container\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use function Hyperf\ViewEngine\view;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\ApplicationContext;
use Core\Common\Container\Auth;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var Response
     */
    protected $response;

    /**
     * @Inject
     * @var SessionInterface
     */
    protected $session;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validation;

    /**
     * @Inject()
     * @var Auth
     */
    protected $auth;

    /**
     * 模板渲染
     * /index/index/index : 绝对路径
     * index : 相对路径
     * view
     * @param array $params
     * @param string $name
     * @return \Hyperf\ViewEngine\Contract\ViewInterface|null
     * author MengShuai <133814250@qq.com>
     * date 2021/01/06 16:39
     */
    protected function view(array $params = [], string $name = ''): ?\Hyperf\ViewEngine\Contract\ViewInterface
    {
        $action = $this->request->getAttribute(Dispatched::class)->handler->callback;
        if (is_string($action) && strpos($action, '::') !== false) {
            $action = explode("::", $action);
        }
        if (is_string($action) && strpos($action, '@') !== false) {
            $action = explode("@", $action);
        }
        if (substr($name, 0, 1) != '/') {
            $view_path = explode("App/Controller", strtr($action[0], "\\", "/"))[1] . '/' . (($name == '') ? $action[1] : $name);
            $view_path = str_replace("Controller", '', $view_path);
        } else {
            $view_path = $name;
        }

//        var_export("模板路径：" . $view_path . "\r\n");
        return view($view_path, $params);
    }

    /**
     * 接口服务信息
     * getServiceInfo
     * @param array $key
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/12/29 10:42
     */
    protected function getServiceInfo(array $key = []): array
    {
        $config = [
            'routePath'       => '/' . $this->request->path(),
            'interfaceDomain' => $this->request->getHeaders()['host'][0] ?? env('API_HOME_INTERFACE'),
            'url'             => $this->request->url(),
            'fullUrl'         => $this->request->fullUrl(),
        ];

        if (!empty($key)) {
            foreach ($config as $k => $vo) {
                if (!in_array($k, $key)) {
                    unset($config[$k]);
                }
            }
            unset($vo);
        }
        //        var_export(['getServiceInfo' => $config]);
        return $config;
    }
}
