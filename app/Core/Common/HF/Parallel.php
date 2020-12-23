<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * Parallel.php
 *
 * User：YM
 * Date：2020/4/18
 * Time：8:48 PM
 */


namespace Core\Common\HF;

use Swoole\Coroutine\Channel;
use Hyperf\Utils\Exception\ParallelExecutionException;
use Hyperf\Utils\Context;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\WaitGroup;



/**
 * Parallel
 * 修改hperf底层支持，父协程传出数据到子协程
 * @package Core\Common\HF
 * User：YM
 * Date：2020/4/18
 * Time：8:48 PM
 */
class Parallel
{
    /**
     * @var callable[]
     */
    private $callbacks = [];

    /**
     * @var null|Channel
     */
    private $concurrentChannel;


    public function __construct(int $concurrent = 0)
    {
        if ($concurrent > 0) {
            $this->concurrentChannel = new Channel($concurrent);
        }
    }

    public function add(callable $callable, $key = null)
    {
        if (is_null($key)) {
            $this->callbacks[] = $callable;
        } else {
            $this->callbacks[$key] = $callable;
        }
    }

    /**
     * getContext
     * 获取需要复制的协程上下文
     * User：YM
     * Date：2020/4/18
     * Time：9:05 PM
     * @return array
     */
    public function getContext(): array
    {
        $data = [];
        foreach (config('context.copy', []) as $key) {
            $data[$key] = Context::get($key);
        }
        // 如果是http请求，进行协程数据复制，做用日志
        if (Context::get('http_request_flag') === true) {
            $data['http_request_flag'] = Context::get('http_request_flag');
        }
        return $data;
    }

    /**
     * setContext
     * 设置协程上下文
     * User：YM
     * Date：2020/4/18
     * Time：9:06 PM
     * @param array $data
     */
    public function setContext(array $data): void
    {
        foreach ($data as $key => $value) {
            Context::set($key, $value);
        }
    }

    public function wait(bool $throw = true): array
    {
        $result = $throwables = [];
        $data = $this->getContext();
        $wg = new WaitGroup();
        $wg->add(count($this->callbacks));
        foreach ($this->callbacks as $key => $callback) {
            $this->concurrentChannel && $this->concurrentChannel->push(true);
            Coroutine::create(function () use ($callback, $key, $wg, &$result, &$throwables, $data) {
                try {
                    $this->setContext($data);
                    $result[$key] = call($callback);
                } catch (\Throwable $throwable) {
                    $throwables[$key] = $throwable;
                } finally {
                    $this->concurrentChannel && $this->concurrentChannel->pop();
                    $wg->done();
                }
            });
        }
        $wg->wait();
        if ($throw && ($throwableCount = count($throwables)) > 0) {
            $message = 'Detecting ' . $throwableCount . ' throwable occurred during parallel execution:' . PHP_EOL . $this->formatThrowables($throwables);
            $executionException = new ParallelExecutionException($message);
            $executionException->setResults($result);
            $executionException->setThrowables($throwables);
            throw $executionException;
        }
        return $result;
    }


    public function clear(): void
    {
        $this->callbacks = [];
    }

    /**
     * Format throwables into a nice list.
     *
     * @param \Throwable[] $throwables
     * @return string
     */
    private function formatThrowables(array $throwables): string
    {
        $output = '';
        foreach ($throwables as $key => $value) {
            $output .= \sprintf('(%s) %s: %s' . PHP_EOL . '%s' . PHP_EOL, $key, get_class($value), $value->getMessage(), $value->getTraceAsString());
        }
        return $output;
    }

}