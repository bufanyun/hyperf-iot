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

use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\SessionInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use function Hyperf\ViewEngine\view;
use Core\Common\Container\Response;
use Core\Common\Container\Auth;
use Psr\Container\ContainerInterface;
use App\Models\Setting;

/**
 *
 * 控制器基类继承
 * Class AbstractController
 *
 * @package App\Controller
 * author MengShuai <133814250@qq.com>
 * date 2021/01/06 23:02
 *
 * @property ContainerInterface $container
 * @property RequestInterface $request
 * @property Response $response
 * @property SessionInterface $session
 * @property ValidatorFactoryInterface $validation
 * @property Auth $auth
 * @property Setting $setting
 */
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
     * @Inject()
     * @var Setting
     */
    protected $setting;

    /**
     * 模板渲染
     * /index/index/index : 绝对路径
     * index : 相对路径
     * view
     *
     * @param array $params
     * @param string $name
     *
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
            $view_path = explode("App/Controller",
                    strtr($action[0], "\\", "/"))[1] . '/' . (($name == '') ? $action[1] : $name);
            $view_path = str_replace("Controller", '', $view_path);
        } else {
            $view_path = $name;
        }

        return view($view_path, $params);
    }

    /**
     * 获取数据限制条件
     * getDataLimitField
     *
     * @param \App\Models\BaseModel|null $model
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/06 17:04
     */
    protected function getDataLimitField($model = null): array
    {
        $admin_id = $this->auth->check(false);
        $field    = 'admin_id';
        if (env('SUPER_ADMIN') === $admin_id) {
            return [];
        }
        if (isset($model) && ($model instanceof \App\Models\BaseModel)) {
            $field = $model->getTable() . '.' . $field;
        }
        return [$field => $admin_id];
    }

    /**
     * 判断管理员是否是超管
     * isSuperAdmin
     *
     * @param null $admin_id
     *
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/06 17:16
     */
    protected function isSuperAdmin(string $admin_id = null): bool
    {
        if (isset($admin_id)) {
            return (env('SUPER_ADMIN') === $admin_id) ?? false;
        }
        return (env('SUPER_ADMIN') === $this->auth->check(false)) ?? false;
    }

    /**
     * 接口服务信息
     * getServiceInfo
     *
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
        return $config;
    }
}
