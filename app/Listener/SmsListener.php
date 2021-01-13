<?php
namespace App\Listener;

use App\Event\SmsEvent;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener
 */
class SmsListener implements ListenerInterface
{
    public function listen(): array
    {
        // 返回一个该监听器要监听的事件数组，可以同时监听多个事件
        return [
            SmsEvent::class,
        ];
    }

    /**
     * @param SmsEvent $event
     */
    public function process(object $event)
    {
        // 事件触发后该监听器要执行的代码写在这里，比如该示例下的发送用户注册成功短信等
        // 直接访问 $event 的 user 属性获得事件触发时传递的参数值
        // $event->user;
        var_export(['$event' => $event->sms]);

        return 'test';
    }
}
