<?php

declare(strict_types=1);

namespace Core\Plugins\WeChat;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\Auth;
use Core\Common\Container\Redis;
use Core\Services\AttachmentService;
use Hyperf\Di\Annotation\Inject;
use Naixiaoxin\HyperfWechat\EasyWechat;

/**
 * 微信公众号
 * Class WeChat
 *
 * @package Core\Plugins
 * author MengShuai <133814250@qq.com>
 * date 2021/01/09 17:17
 * @property \EasyWeChat\OfficialAccount\Application $app
 */
class OfficialAccount extends Base
{
    /**
     * @Inject()
     * @var Redis
     */
    private $Redis;

    public $app = null;

    public function __construct()
    {
        $this->app = EasyWechat::officialAccount();
    }

    /**
     * 生成短连接
     * shorten
     *
     * @param string $url
     * @param string $link
     *
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * author MengShuai <133814250@qq.com>
     * date 2021/01/17 14:04
     */
    public function shorten(string $url, string $link = ''): string
    {
        $key = RedisCode::SHORTEN_LINK . buildStringHash(json_encode($url));
        if ($res = $this->Redis->get($key)) {
            return $res;
        }
        $res = $this->app->url->shorten($url);
        if (isset($res['errcode']) && $res['errcode'] === 0) {
            $link = $res['short_url'];
        }
        if ($link) {
            $this->Redis->set($key, $link, 86400 * 7);
        }
        return $link;
    }

    /**
     * 发送模板消息给指定用户
     * template_message
     *
     * @param array $data
     *
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * author MengShuai <133814250@qq.com>
     * date 2021/01/12 09:30
     */
    public function template_message(array $data): array
    {
        return $this->app->template_message->send([
            'touser'      => $data['touser'],
            'template_id' => $data['template_id'],
            'url'         => $data['url'],
//            'miniprogram' => [  //跳转小程序
//                'appid' => 'xxxxxxx',
//                'pagepath' => 'pages/xxx',
//            ],
            'data'        => $data['data'],
        ]);
    }

}