<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\User;
use Hyperf\Di\Annotation\Inject;
use Core\Plugins\Sms;
use App\Constants\StatusCode;

/**
 * ApiController
 * 管理员api
 * @package App\Controller\Admin
 *
 * @Controller(prefix="/admin_api/api")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property User $model
 * @property Sms $Sms
 */
class ApiController extends BaseController
{
    /**
     * @Inject()
     * @var User
     */
    private $model;

    /**
     * @Inject()
     * @var Sms
     */
    protected $Sms;

    /**
     * 发送短信
     * send_sms
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="send_sms")
     */
    public function send_sms()
    {
        $reqParam  = $this->request->all();
        $validator = $this->validation->make(
            $reqParam,
            [
                'mobile' => 'required|regex:/^1[34578]\d{9}$/',
                'event'  => 'required',
            ],
            [
                'mobile.required' => '手机号不能为空',
                'mobile.regex' => '手机号格式不正确',
                'event.required'  => '事件模板不能为空',
            ]
        );
        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }
        if(!$this->Sms->getTemplate($reqParam['event'])){
            return $this->error(StatusCode::ERR_EXCEPTION, '事件模板未注册');
        }

        $last = $this->Sms->get($reqParam['mobile'], $reqParam['event']);
        if ($last && time() - strtotime((string)$last->created_at) < 60) {
            return $this->error(StatusCode::ERR_EXCEPTION, '发送频繁，请60秒后再试');
        }
        $ipSendTotal = $this->Sms->getIpFrequency(getClientIp());
        if ($ipSendTotal >= 10) {
            return $this->error(StatusCode::ERR_EXCEPTION, '发送频繁，请一小时后再试');
        }

        if($reqParam['event'] === 'register')
        {
            //TODO
            //...
        }

        $res = $this->Sms->send($reqParam['mobile'], $code = null, $reqParam['event']);
        if(!$res){
            return $this->error(StatusCode::ERR_EXCEPTION, '发送失败，稍后再试！');
        }
        return $this->success([], '发送成功');
    }


    public function check_sms()
    {

    }
}