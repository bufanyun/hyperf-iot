<?php
/**
 * Created by PhpStorm.
 *​
 * BaseService.php
 *
 * 服务基类
 *
 * User：YM
 * Date：2019/11/21
 * Time：下午3:21
 */


namespace Core\Services;

use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use App\Constants\StatusCode;

/**
 * BaseService
 * 服务基类
 * @package Core\Services
 * User：YM
 * Date：2019/11/21
 * Time：下午3:21
 */
class BaseService
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Created by PhpStorm.
     * 可以实现自动注入的业务容器
     * User：YM
     * Date：2020/1/12
     * Time：上午8:18
     */
    protected $businessContainerKey = ['auth','adminPermission'];

    /**
     * __get
     * 隐式注入服务类
     * User：YM
     * Date：2019/11/21
     * Time：上午9:27
     * @param $key
     * @return \Psr\Container\ContainerInterface|void
     */
    public function __get($key)
    {
        if ($key == 'app') {
            return $this->container;
        } elseif (in_array($key,$this->businessContainerKey)) {
            return $this->getBusinessContainerInstance($key);
        } elseif (substr($key, -5) == 'Model') {
            $key = strstr($key,'Model',true);
            return $this->getModelInstance($key);
        } elseif (substr($key, -7) == 'Service') {
            return $this->getServiceInstance($key);
        } else {
            throw new \RuntimeException("服务/模型{$key}不存在，书写错误！", StatusCode::ERR_SERVER);
        }
    }

    /**
     * getBusinessContainerInstance
     * 获取业务容器实例
     * User：YM
     * Date：2020/1/12
     * Time：上午8:15
     * @param $key
     * @return mixed
     */
    public function getBusinessContainerInstance($key)
    {
        $key = ucfirst($key);
        $fileName = BASE_PATH."/app/Core/Common/Container/{$key}.php";
        $className = "Core\\Common\\Container\\{$key}";

        if (file_exists($fileName)) {
            return $this->container->get($className);
        } else {
            throw new \RuntimeException("通用容器{$key}不存在，文件不存在！", StatusCode::ERR_SERVER);
        }
    }

    /**
     * getModelInstance
     * 获取数据模型类实例
     * User：YM
     * Date：2019/11/21
     * Time：上午10:30
     * @param $key
     * @return mixed
     */
    public function getModelInstance($key)
    {
        $key = ucfirst($key);
        $fileName = BASE_PATH."/app/Models/{$key}.php";
        $className = "App\\Models\\{$key}";

        if (file_exists($fileName)) {
            //model一般不要常驻内存
            //return $this->container->get($className);
            return make($className);
        } else {
            throw new \RuntimeException("服务/模型{$key}不存在，文件不存在！", StatusCode::ERR_SERVER);
        }
    }

    /**
     * getServiceInstance
     * 获取服务类实例
     * User：YM
     * Date：2019/11/21
     * Time：上午10:30
     * @param $key
     * @return mixed
     */
    public function getServiceInstance($key)
    {
        $key = ucfirst($key);
        $fileName = BASE_PATH."/app/Core/Services/{$key}.php";
        $className = "Core\\Services\\{$key}";

        if (file_exists($fileName)) {
            return $this->container->get($className);
        } else {
            throw new \RuntimeException("服务/模型{$key}不存在，文件不存在！", StatusCode::ERR_SERVER);
        }
    }
}