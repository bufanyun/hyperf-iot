<?php
/**
 * Created by VIM.
 * Author:YQ
 * Date:2020/01/07 18:49:31
 */

declare(strict_types=1);

namespace Core\Common\Handler;

use Hyperf\Cache\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Hyperf\Redis\RedisProxy;
use Hyperf\Cache\Driver\Driver;
use Hyperf\Cache\Driver\KeyCollectorInterface;

class CacheRedisHandler extends Driver implements KeyCollectorInterface
{
    /**
     * @var \Redis
     */
    protected $redis;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);

        $this->redis = make(RedisProxy::class, ['pool' => $config['pool']]);
    }

    public function get($key, $default = null)
    {
        $res = $this->redis->get($this->getCacheKey($key));
        if ($res === false) {
            return $default;
        }

        return $this->packer->unpack($res);
    }

    public function fetch(string $key, $default = null): array
    {
        $res = $this->redis->get($this->getCacheKey($key));
        if ($res === false) {
            return [false, $default];
        }

        return [true, $this->packer->unpack($res)];
    }

    public function set($key, $value, $ttl = null)
    {
        $seconds = $this->secondsUntil($ttl);
        $res = $this->packer->pack($value);
        if ($seconds > 0) {
            return $this->redis->set($this->getCacheKey($key), $res, $seconds);
        }

        return $this->redis->set($this->getCacheKey($key), $res);
    }

    public function delete($key)
    {
        return (bool) $this->redis->del($this->getCacheKey($key));
    }

    public function clear()
    {
        return $this->clearPrefix('');
    }

    public function getMultiple($keys, $default = null)
    {
        $cacheKeys = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        $values = $this->redis->mget($cacheKeys);
        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $values[$i] === false ? $default : $this->packer->unpack($values[$i]);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (! is_array($values)) {
            throw new InvalidArgumentException('The values is invalid!');
        }

        $cacheKeys = [];
        foreach ($values as $key => $value) {
            $cacheKeys[$this->getCacheKey($key)] = $this->packer->pack($value);
        }

        $seconds = $this->secondsUntil($ttl);
        if ($seconds > 0) {
            foreach ($cacheKeys as $key => $value) {
                $this->redis->set($key, $value, $seconds);
            }

            return true;
        }

        return $this->redis->mset($cacheKeys);
    }

    public function deleteMultiple($keys)
    {
        $cacheKeys = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        return $this->redis->del(...$cacheKeys);
    }

    public function has($key)
    {
        return (bool) $this->redis->exists($this->getCacheKey($key));
    }

    public function clearPrefix(string $prefix): bool
    {
        $iterator = null;
        $key = $prefix . '*';
        while (true) {
            $keys = $this->redis->scan($iterator, $this->getCacheKey($key), 10000);
            if (! empty($keys)) {
                $this->redis->del(...$keys);
            }

            if (empty($iterator)) {
                break;
            }
        }

        return true;
    }

    public function addKey(string $collector, string $key): bool
    {
        return (bool) $this->redis->sAdd($this->getCacheKey($collector), $key);
    }

    public function keys(string $collector): array
    {
        return $this->redis->sMembers($this->getCacheKey($collector)) ?? [];
    }

    public function delKey(string $collector, ...$key): bool
    {
        return (bool) $this->redis->sRem($this->getCacheKey($collector), ...$key);
    }
}
