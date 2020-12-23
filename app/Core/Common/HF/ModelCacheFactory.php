<?php
/**
 * Created by VIM.
 * Author:YQ
 * Date:2020/01/07 09:33:20
 */
declare(strict_types=1);

namespace Core\Common\HF;

use Psr\Container\ContainerInterface;
use Hyperf\ModelCache\Handler\HandlerInterface;
use Hyperf\ModelCache\Handler\RedisHandler;
use Core\Common\Handler\ModelCacheFileHandler;
use Hyperf\ModelCache\Config;

/**
 * ModelCacheFactory
 * 数据模型缓存工厂
 * package Core\Common\HF
 * date 2020-01-07
 * @author YQ
 */
class ModelCacheFactory  implements HandlerInterface
{
    private $cacheInstance = null;

    public function __construct(ContainerInterface $container, Config $config)
    {
        if($this->cacheInstance == null){
            $driver = env('MODEL_CACHE_DRIVER', 'file');
            $this->cacheInstance = $this->getCacheInstance($driver, $container, $config);
        }
    }

    /**
     * getCacheInstance
     * 获取缓存驱动实例
     * @param mixed $driver
     * @param ContainerInterface $container
     * @param array $config
     * @access private
     * @return class
     * Date: 2020-01-07
     * Created by YQ
     */
    private function getCacheInstance($driver, ContainerInterface $container, Config $config)
    {
        switch($driver){
            case 'file':
                return make(ModelCacheFileHandler::class, [$container, $config]);
            case 'redis':
                return make(RedisHandler::class, [$container, $config]);
            default:
                throw new \RuntimeException("model cache [$driver] not found");
        }
    }

    public function get($key, $default = null)
    {
        return $this->cacheInstance->get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->cacheInstance->set($key, $value, $ttl);
    }

    public function delete($key)
    {
        return $this->cacheInstance->delete($key);
    }

    public function clear()
    {
        return $this->cacheInstance->clear();
    }

    public function getMultiple($keys, $default = null)
    {
        return $this->cacheInstance->getMultiple($keys, $default);
    }

    public function setMultiple($values, $ttl = null)
    {
        return $this->cacheInstance->setMultiple($values, $ttl);
    }

    public function deleteMultiple($keys)
    {
        return $this->cacheInstance->deleteMultiple($keys);
    }

    public function has($key)
    {
        return $this->cacheInstance->has($key);
    }

    public function getConfig(): Config
    {
        return $this->cacheInstance->getConfig();
    }

    public function incr($key, $column, $amount): bool
    {
        return $this->cacheInstance->incr($key, $column, $amount);
    }

    public function __call($name, $arguments)
    {
        return $this->cacheInstance->$name(...$arguments);
    }
}
