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
 * @property OfficialAccount $OfficialAccount
 * @property Sms $Sms
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


    /**
     * 微信模版消息测试
     * @RequestMapping(path="test")
     *
     */
    public function test()
    {
        $app = EasyWechat::officialAccount();
        $openId = 'ok1EU6l49YgIm66DzPqVzKYNiQvk';
        $app->template_message->send(
            [
                'touser'      => $openId,
                'template_id' => 'bUVLDifDBOOmR_0dmzQK8ixfzDZQXBY9uT_xKegza5g',
                'url'         => 'https://easywechat.org',
//                'miniprogram' => [
//                    'appid'    => 'wxa0f66fc9734bbda2',
//                    'pagepath' => 'pages/sdkDemo/appletsLogin',
//                ],
                'data'        => [
                    'first' => '测试标题',
                    'keyword1' => 'keyword1',
                    'keyword2' => 'keyword2 ',
                    'keyword3' => 'keyword3',
                    'remark' => 'remark',
//                    ...
                ],
            ]
        );
    }

}