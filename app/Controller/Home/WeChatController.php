<?php

declare(strict_types=1);

namespace App\Controller\Home;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Naixiaoxin\HyperfWechat\EasyWechat;
use Naixiaoxin\HyperfWechat\Helper;
use ReflectionException;
use App\Controller\BaseController;
use App\Constants\StatusCode;
use EasyWeChat\Kernel\Messages\Text;
use Core\Plugins\WeChat\OfficialAccount;

class WeChatController extends BaseController
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws ReflectionException
     */
    public function serve()
    {
        var_export('=========微信回调开始======');
        $reqParam = $this->request->all();
        var_export($reqParam);
        $app = EasyWechat::officialAccount();
        $app->server->push(function ($message) use($app){
            if(empty($message)){
                return "你好，欢迎关注千里号卡！";
            }
            var_export(['OK',$message]);
            $user = $app->user->get($message['FromUserName']);
            return "{$user['nickname']} 你好，欢迎关注千里号卡！";
        });
        $openId = 'ok1EU6l49YgIm66DzPqVzKYNiQvk';
        $message = make(Text::class,['Hello world!!']);
        $result = $app->customer_service->message($message)->to($openId)->send();
        var_export(['$result' => $result]);
        var_export('=========微信回调结束======');
        // 一定要用Helper::Response去转换
        return Helper::Response($app->server->serve());
    }
}