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
 *
 * Class WeChat
 * @package Core\Plugins
 * author MengShuai <133814250@qq.com>
 * date 2021/01/09 17:17
 * @property \Naixiaoxin\HyperfWechat\EasyWechat $app
 */
class OfficialAccount
{
    private $app;

    public function __construct()
    {
        $this->app = EasyWechat::officialAccount();
    }

    /**
     * 生成短连接
     * shorten
     * @param string $url
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/09 17:51
     */
    public function shorten(string $url) : array
    {
        $res = $this->app->url->shorten($url);
        var_export(['$res' => $res]);
        return $res;
    }
    
}