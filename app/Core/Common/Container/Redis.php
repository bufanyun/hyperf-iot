<?php

declare(strict_types=1);

namespace Core\Common\Container;

use Hyperf\Utils\ApplicationContext;

/**
 * redis操作类
 * Class RedisDriver
 * @package Core\Common\Driver
 */
class Redis
{
    private $cacheInstance = null;

    public function __construct()
    {
        if ($this->cacheInstance == null) {
            $this->cacheInstance = $this->getCacheInstance();
        }
    }

    /**
     * 获取缓存驱动实例
     * @return \Hyperf\Redis\Redis|mixed
     */
    private function getCacheInstance(): ?\Hyperf\Redis\Redis
    {
        $container = ApplicationContext::getContainer();
        $redis     = $container->get(\Hyperf\Redis\Redis::class);
        return $redis;
    }

    public function getCacheKey(string $key)
    {
        return $this->cacheInstance->getCacheKey($key);
    }

    public function get($key, $default = null)
    {
        return $this->cacheInstance->get($key);
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
        return $this->cacheInstance->getMultiple($keys);
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
