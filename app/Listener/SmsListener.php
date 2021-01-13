<?php
namespace App\Listener;

use App\Event\SmsEvent;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Di\Annotation\Inject;
use App\Models\Sms as SmsModel;

/**
 * 发送短信事件
 * Class SmsListener
 * @package App\Listener
 * author MengShuai <133814250@qq.com>
 * date 2021/01/13 15:53
 *
 * @Listener
 * @property SmsModel $SmsModel
 */
class SmsListener implements ListenerInterface
{

    /**
     * @Inject()
     * @var SmsModel
     */
    protected $SmsModel;

    public function listen(): array
    {
        return [
            SmsEvent::class,
        ];
    }

    /**
     * @param SmsEvent $event
     */
    public function process(object $event)
    {
        $this->{$event->function}($event->data);
    }

    /**
     * 发送短信
     * send
     * @param array $event
     * author MengShuai <133814250@qq.com>
     * date 2021/01/13 15:53
     */
    protected function send(array $event) : void
    {
        $this->SmsModel->query()->insert($event);
        // TODO ..
    }
}
