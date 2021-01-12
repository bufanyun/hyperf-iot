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

/**
 * IndexController
 * 通知控制器
 * 接收处理三方通知
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/index")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 * @property OfficialAccount $OfficialAccount
 */
class IndexController extends BaseController
{
    /**
     * @Inject()
     * @var OfficialAccount
     */
    protected $OfficialAccount;

    /**
     * index
     * 微信扫码设备
     * @RequestMapping(path="index")
     *
     * Middleware(OssCallbackMiddleware::class)
     */

    public function index()
    {
        $res = Db::connection('bufan')->table('withdraw')
//        ->where(['status' => 1])
//        ->orderBy('product_classify.sort', 'DESC')
            ->get();
        var_export($res);
        return $this->view(['name' => 'ms']);
    }

    /**
     * test
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="test")
     *
     * Middleware(OssCallbackMiddleware::class)
     */

    public function test()
    {
        $openId = 'ok1EU6l49YgIm66DzPqVzKYNiQvk';
        $data = [
            'touser' => $openId,
            'template_id' => 'Upmm3bET5d3pXt2nhfxhg8pF0wgdztCiiZP8Kx_btko',
            'url' => 'http://card.facms.cn/#/pages/spread/index?r=/home/spread/pool&job_number=bufanyun&channel=3&sub_agent=1',
            'data' => [
                'first' => '尊敬的合伙人，您有新的推广订单已完成。',
                'keyword1' => ['15303830***888(大王卡)', '#771caa'],
                'keyword2' => ['20.00元', '#771caa'],
                'keyword3' => ['2021-01-12', '#771caa'],
                'remark' => ['该笔订单预计还有T+1月在网奖励10元，T+2月在网奖励10元，该订购号码持续在网就会自动发放哦！', '#771caa'],
            ],
        ];
        $this->OfficialAccount->template_message($data);
    }

}