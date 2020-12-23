<?php
/**
 * Created by PhpStorm.
 *​
 * EventDispatcher.php
 *
 * 事件处理
 *
 * User：YM
 * Date：2019/12/21
 * Time：上午11:30
 */


declare(strict_types=1);


namespace Core\Common\HF;

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;


/**
 * EventDispatcher
 * 事件处理,改变框架原来的事件处理逻辑
 * @package Core\Common\HF
 * User：YM
 * Date：2019/12/21
 * Time：上午11:48
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ListenerProviderInterface
     */
    private $listeners;

    /**
     * @var null|StdoutLoggerInterface
     */
    private $logger;

    public function __construct(
        ListenerProviderInterface $listeners,
        ?StdoutLoggerInterface $logger = null
    ) {
        $this->listeners = $listeners;
        $this->logger = $logger;
    }

    /**
     * Provide all listeners with an event to process.
     *
     * @param object $event The object to process
     * @return object The Event that was passed, now modified by listeners
     */
    public function dispatch(object $event)
    {
        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listener($event);
            $this->dump($listener, $event);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }
        return $event;
    }

    /**
     * dump
     * 函数的含义说明
     * User：YM
     * Date：2019/12/21
     * Time：上午11:49
     * @param $listener
     * @param object $event
     */
    private function dump($listener, object $event)
    {
        if (! $this->logger instanceof StdoutLoggerInterface) {
            return;
        }
        $eventName = get_class($event);
        $listenerName = '[ERROR TYPE]';
        if (is_array($listener)) {
            $listenerName = is_string($listener[0]) ? $listener[0] : get_class($listener[0]);
        } elseif (is_string($listener)) {
            $listenerName = $listener;
        } elseif (is_object($listener)) {
            $listenerName = get_class($listener);
        }

        $this->logger->debug(sprintf('Event %s handled by %s listener.', $eventName, $listenerName));
    }
}
