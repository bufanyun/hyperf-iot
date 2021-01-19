<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;
use App\Models\Log;
use Hyperf\Di\Annotation\Inject;
use App\Exception\DatabaseExceptionHandler;


/**
 * 消费者 - 日志记录
 *
 * @Consumer(exchange="logs", routingKey="logs", queue="logs", name ="LogsConsumer", nums=1, maxConsumption=2000)
 * @property Log $LogModel
 */
class LogsConsumer extends ConsumerMessage
{
    /**
     * @Inject()
     * @var Log
     */
    private $LogModel;

    public function consumeMessage($data, AMQPMessage $message): string
    {
        if(!is_array($data)){
            return Result::NACK;
        }

        if (!$this->LogModel->add($data)) {
            return Result::DROP;
        }

        return Result::ACK;
    }
}
