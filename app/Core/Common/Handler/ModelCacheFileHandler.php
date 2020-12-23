<?php
/**
 * Created by VIM.
 * Author:YQ
 * Date:2020/01/03 15:48:03
 */

declare(strict_types=1);

namespace Core\Common\Handler;

use Hyperf\ModelCache\Config;
use Hyperf\Cache\Collector\FileStorage;
use Hyperf\Cache\Exception\CacheException;
use Hyperf\Cache\Exception\InvalidArgumentException;
use Hyperf\Utils\Packer\PhpSerializerPacker;
use Psr\Container\ContainerInterface;
use Hyperf\Cache\Driver\Driver;
use Hyperf\ModelCache\Handler\HandlerInterface;
use Hyperf\Utils\InteractsWithTime;

class ModelCacheFileHandler implements HandlerInterface
{
    use InteractsWithTime;

    /**
     * @var string
     */
    protected $storePath = BASE_PATH . '/runtime/model_caches';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var PackerInterface
     */
    protected $packer;

    public function __construct(ContainerInterface $container, Config $config)
    {
        if (! file_exists($this->storePath)) {
            $results = mkdir($this->storePath, 0777, true);
            if (! $results) {
                throw new CacheException('Has no permission to create cache directory!');
            }
        }
        $this->container = $container;
        $this->config = $config;
        $packerClass = PhpSerializerPacker::class;
        $this->packer = $container->get($packerClass);
    }

    public function getCacheKey(string $key)
    {
        return $this->getStorePathLevel($key) . $this->getPrefix() . $key . '.cache';
    }

    public function get($key, $default = null)
    {
        $file = $this->getCacheKey($key);
        if (! file_exists($file)) {
            return $default;
        }

        /** @var FileStorage $obj */
        $obj = $this->packer->unpack(file_get_contents($file));
        if ($obj->isExpired()) {
            return $default;
        }

        return $obj->getData();
    }

    public function fetch(string $key, $default = null): array
    {
        $file = $this->getCacheKey($key);
        if (! file_exists($file)) {
            return [false, $default];
        }

        /** @var FileStorage $obj */
        $obj = $this->packer->unpack(file_get_contents($file));
        if ($obj->isExpired()) {
            return [false, $default];
        }

        return [true, $obj->getData()];
    }

    public function set($key, $value, $ttl = null)
    {
        $seconds = $this->secondsUntil($ttl);
        $file = $this->getCacheKey($key);
        $content = $this->packer->pack(new FileStorage($value, $seconds));

        $result = file_put_contents($file, $content, FILE_BINARY);

        return (bool) $result;
    }

    public function delete($key)
    {
        $file = $this->getCacheKey($key);
        if (file_exists($file)) {
            if (! is_writable($file)) {
                return false;
            }
            unlink($file);
        }

        return true;
    }

    public function clear()
    {
        return $this->clearPrefix('');
    }

    public function getMultiple($keys, $default = null)
    {
        if (! is_array($keys)) {
            throw new InvalidArgumentException('The keys is invalid!');
        }

        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (! is_array($values)) {
            throw new InvalidArgumentException('The values is invalid!');
        }
        $seconds = $this->secondsUntil($ttl);
        foreach ($values as $key => $value) {
            $this->set($key, $value, $seconds);
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        if (! is_array($keys)) {
            throw new InvalidArgumentException('The keys is invalid!');
        }

        foreach ($keys as $index => $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has($key)
    {
        $file = $this->getCacheKey($key);

        return file_exists($file);
    }

    public function clearPrefix(string $prefix): bool
    {
        $storePath = $this->getStorePathLevel();
        $this->clearFileCache($storePath, $prefix);
        return true;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function incr($key, $column, $amount): bool
    {
        $value = $this->get($key);
        if($value && isset($value[$column])){
            $value[$column] = $value[$column]+1;
            $this->set($key, $value);
            return true;
        }else{
            throw new CacheException('Cache incr value err!');
        }
    }

    protected function clearFileCache($path, string $prefix)
    {
        $dirs = scandir($path);
        foreach($dirs as $file){
            if($file != '.' && $file != '..'){
                if (is_dir($path.$file)) {
                    $this->clearFileCache($path.$file.DIRECTORY_SEPARATOR, $prefix);
                }else{
                    if(fnmatch($this->getPrefix() . $prefix . '*', $file)){
                        unlink($path.$file);
                    }
                }
            }
        }

        return true;
    }

    protected function getStorePathLevel($key = null)
    {
        if($key){
            $file = md5($key);
            $levelPath1 = substr($file, 0, 2);
            $levelPath2 = substr($file, 2, 2);
            $fullPath = $this->storePath. DIRECTORY_SEPARATOR. $levelPath1. DIRECTORY_SEPARATOR. $levelPath2. DIRECTORY_SEPARATOR;
            if (! file_exists($fullPath)) {
                $results = mkdir($fullPath, 0777, true);
                if (! $results) {
                    throw new CacheException('Has no permission to create cache directory!');
                }
            }
        }else{
            return $this->storePath. DIRECTORY_SEPARATOR;
        }
        return $fullPath;
    }

    protected function getPrefix()
    {
        return $this->prefix;
    }
}
