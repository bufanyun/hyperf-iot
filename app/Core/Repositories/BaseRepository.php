<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * BaseRepository.php
 *
 * 仓库基类
 *
 * User：YM
 * Date：2019/11/21
 * Time：下午2:36
 */


namespace Core\Repositories;


use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use App\Constants\StatusCode;

/**
 * BaseRepository
 * 仓库基类
 * @package Core\Repositories
 * User：YM
 * Date：2019/11/21
 * Time：下午2:36
 */
class BaseRepository
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
        }elseif (substr($key, -7) == 'Service') {
            return $this->getServiceInstance($key);
        } else {
            throw new \RuntimeException("服务{$key}不存在，书写错误！", StatusCode::ERR_SERVER);
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
            throw new \RuntimeException("服务{$key}不存在，文件不存在！", StatusCode::ERR_SERVER);
        }
    }
}