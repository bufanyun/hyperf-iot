<?php

declare(strict_types=1);

namespace Core\Plugins\WeChat;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\Auth;
use Core\Services\AttachmentService;
use Hyperf\Di\Annotation\Inject;
use Naixiaoxin\HyperfWechat\EasyWechat;


class Base extends EasyWechat
{

}