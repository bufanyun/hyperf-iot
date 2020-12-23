<?php
/**
 * Created by VIM.
 * Author:YQ
 * Date:2020/01/07 09:33:20
 */
declare(strict_types=1);

namespace Core\Common\Driver;

use Psr\Container\ContainerInterface;
use Core\Common\Handler\CacheFileHandler;
use Core\Common\Handler\CacheRedisHandler;
use Hyperf\Cache\Driver\Driver;

/**
 * CacheDriver
 * 缓存驱动
 * package Core\Common\HF
 * date 2020-01-07
 * @author YQ
 */
class CacheDriver extends Driver
{
    private $cacheInstance = null;

    public function __construct(ContainerInterface $container, array $config)
    {
        if($this->cacheInstance == null){
            $driver = env('CACHE_DRIVER', 'file');
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
    private function getCacheInstance($driver, ContainerInterface $container, array $config)
    {
        switch($driver){
            case 'file':
                return make(CacheFileHandler::class, [$container, $config]);
            case 'redis':
                return make(CacheRedisHandler::class, [$container, $config]);
            default:
                throw new \RuntimeException("cache [$driver] not found");
        }
    }

    public function getCacheKey(string $key)
    {
        return $this->cacheInstance->getCacheKey($key);
    }

    public function get($key, $default = null)
    {
        return $this->cacheInstance->get($key, $default);
    }

    public function fetch(string $key, $default = null): array
    {
        return $this->cacheInstance->fetch($key, $default);
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

    public function clearPrefix(string $prefix): bool
    {
        return $this->cacheInstance->clearPrefix($prefix);
    }

    public function __call($name, $arguments)
    {
        return $this->cacheInstance->$name(...$arguments);
    }
}
