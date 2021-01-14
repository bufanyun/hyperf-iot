<?php
namespace App\Listener;

use App\Event\EmsEvent;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Di\Annotation\Inject;
use App\Models\Ems as EmsModel;

/**
 * 发送邮箱事件
 * Class EmsListener
 * @package App\Listener
 * author MengShuai <133814250@qq.com>
 * date 2021/01/13 15:53
 *
 * @Listener
 * @property EmsModel $EmsModel
 */
class EmsListener implements ListenerInterface
{

    /**
     * @Inject()
     * @var EmsModel
     */
    protected $EmsModel;

    public function listen(): array
    {
        return [
            EmsEvent::class,
        ];
    }

    /**
     * @param EmsEvent $event
     */
    public function process(object $event)
    {
        $this->{$event->function}($event->data);
    }

    /**
     * 发送邮件
     * send
     * @param array $event
     * author MengShuai <133814250@qq.com>
     * date 2021/01/13 15:53
     */
    protected function send(array $event) : void
    {
        $this->EmsModel->query()->insert($event);
        // TODO ..
    }
}
