<?php

declare(strict_types=1);

namespace Core\Plugins\WeChat;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\Auth;
use Core\Services\AttachmentService;
use Hyperf\Di\Annotation\Inject;
use Naixiaoxin\HyperfWechat\EasyWechat;

/**
 * 微信公众号
 * Class WeChat
 * @package Core\Plugins
 * author MengShuai <133814250@qq.com>
 * date 2021/01/09 17:17
 * @property \EasyWeChat\OfficialAccount\Application $app
 */
class OfficialAccount
{
    private ? EasyWechat $app = null;

    public function __construct()
    {
        $this->app = EasyWechat::officialAccount();
    }

    /**
     * 生成短连接
     * shorten
     * @param string $url
     *
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * author MengShuai <133814250@qq.com>
     * date 2021/01/10 00:00
     */
    public function shorten(string $url) : array
    {
        $res = $this->app->url->shorten($url);
        var_export(['$res' => $res]);
        return $res;
    }
    
}