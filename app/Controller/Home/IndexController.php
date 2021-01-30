<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\OssCallbackMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Di\Annotation\Inject;
use Core\Plugins\WeChat\OfficialAccount;
use Naixiaoxin\HyperfWechat\EasyWechat;
use Naixiaoxin\HyperfWechat\Helper;
use ReflectionException;
use EasyWeChat\Kernel\Messages\Text;
use HyperfLibraries\Sms\Contract\SmsInterface;
use Core\Plugins\Sms;
use Core\Plugins\Ems as EmsPlugins;
use Hyperf\Amqp\Producer;
use App\Amqp\Producer\LogsProducer;
use App\Models\Log;

/**
 * IndexController
 * 通知控制器
 * 接收处理三方通知
 *
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/index")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 * @property OfficialAccount                              $OfficialAccount
 * @property Sms                                          $Sms
 */
class IndexController extends BaseController
{
    /**
     * @Inject()
     * @var Log
     */
    private $LogModel;
    /**
     * @Inject()
     * @var OfficialAccount
     */
    protected $OfficialAccount;

    /**
     * @Inject()
     * @var Sms
     */
    protected $Sms;

    /**
     * @Inject()
     * @var EmsPlugins
     */
    protected $EmsPlugins;
    
    /**
     * index
     * 微信扫码设备
     * @RequestMapping(path="index")
     *
     * Middleware(OssCallbackMiddleware::class)
     */

    public function index()
    {
        return $this->view(['name' => 'ms']);
    }

}