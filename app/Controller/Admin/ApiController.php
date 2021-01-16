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
use Core\Plugins\Ems;
use App\Constants\StatusCode;
use Core\Plugins\WeChat\OfficialAccount;

/**
 * ApiController
 * 管理员api
 *
 * @package App\Controller\Admin
 *
 * @Controller(prefix="/admin_api/api")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property User            $model
 * @property Sms             $Sms
 * @property Ems             $Ems
 * @property OfficialAccount $OfficialAccount
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
     * @Inject()
     * @var Ems
     */
    protected $Ems;

    /**
     * @Inject()
     * @var OfficialAccount
     */
    protected $OfficialAccount;


    /**
     * 发送短信
     * send_sms
     *
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
                'mobile' => 'required|regex:/^1[3456789]\d{9}$/',
                'event'  => 'required',
            ],
            [
                'mobile.required' => '手机号不能为空',
                'mobile.regex'    => '手机号格式不正确',
                'event.required'  => '事件模板不能为空',
            ]
        );
        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }
        if (!$this->Sms->getTemplate($reqParam['event'])) {
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

        if ($reqParam['event'] === 'register') {
            //TODO
            //...
        }

        $res = $this->Sms->send($reqParam['mobile'], $code = null, $reqParam['event']);
        if (!$res) {
            return $this->error(StatusCode::ERR_EXCEPTION, '发送失败，稍后再试！');
        }
        return $this->success([], '发送成功');
    }

    /**
     * 效验短信验证码
     * check_sms
     *
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 09:45
     *
     * @RequestMapping(path="check_sms")
     */
    public function check_sms()
    {
        $reqParam  = $this->request->all();
        $validator = $this->validation->make(
            $reqParam,
            [
                'mobile'  => 'required|regex:/^1[3456789]\d{9}$/',
                'event'   => 'required',
                'captcha' => 'required',
            ],
            [
                'mobile.required'  => '手机号不能为空',
                'mobile.regex'     => '手机号格式不正确',
                'event.required'   => '事件模板不能为空',
                'captcha.required' => '验证码不能为空',
            ]
        );
        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }
        if (!$this->Sms->getTemplate($reqParam['event'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '事件模板未注册');
        }
        if ($reqParam['event'] === 'register') {
            //TODO
            //...
        }
        $ret = $this->Sms->check($reqParam['mobile'], $reqParam['captcha'], $reqParam['event']);
        if ($ret) {
            return $this->success([], '验证成功');
        } else {
            return $this->error(StatusCode::ERR_EXCEPTION, '验证码不正确');
        }
    }


    /**
     * 发送邮件
     * send_ems
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="send_ems")
     */
    public function send_ems()
    {
        $reqParam  = $this->request->all();
        $validator = $this->validation->make(
            $reqParam,
            [
                'email'   => 'required|regex:/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',
                'event'   => 'required',
                'content' => 'required',
            ],
            [
                'email.required'   => '邮箱不能为空',
                'email.regex'      => '邮箱格式不正确',
                'event.required'   => '事件模板不能为空',
                'content.required' => '邮箱内容不能为空',
            ]
        );
        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }

        if ($reqParam['event'] !== 'html') {
            $last = $this->Ems->get($reqParam['email'], $reqParam['event']);
            if ($last && time() - strtotime((string)$last->created_at) < 60) {
                return $this->error(StatusCode::ERR_EXCEPTION, '发送频繁，请60秒后再试');
            }
            $ipSendTotal = $this->Ems->getIpFrequency(getClientIp());
            if ($ipSendTotal >= 10) {
                return $this->error(StatusCode::ERR_EXCEPTION, '发送频繁，请一小时后再试');
            }
        }

        $Subject = '系统通知';
        if ($reqParam['event'] === 'register') {
            $Subject = '新用户注册验证';
        }

        $res = $this->Ems->send([
            'Subject'    => $Subject,
            'MsgHTML'    => $reqParam['content'],
            'AddAddress' => $reqParam['email'],
        ], $reqParam['event']);
        if (!$res) {
            return $this->error(StatusCode::ERR_EXCEPTION, '发送失败，稍后再试！');
        }
        return $this->success([], '发送成功');
    }

    /**
     * 效验邮箱验证码
     * check_ems
     *
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 09:45
     *
     * @RequestMapping(path="check_ems")
     */
    public function check_ems()
    {
        $reqParam  = $this->request->all();
        $validator = $this->validation->make(
            $reqParam,
            [
                'email'   => 'required|regex:/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/',
                'event'   => 'required',
                'captcha' => 'required',
            ],
            [
                'email.required'   => '邮箱不能为空',
                'email.regex'      => '邮箱格式不正确',
                'event.required'   => '事件模板不能为空',
                'captcha.required' => '验证码不能为空',
            ]
        );
        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }
        if ($reqParam['event'] === 'register') {
            //TODO
            //...
        }
        $ret = $this->Ems->check($reqParam['email'], $reqParam['captcha'], $reqParam['event']);
        if ($ret) {
            return $this->success([], '验证成功');
        } else {
            return $this->error(StatusCode::ERR_EXCEPTION, '验证码不正确');
        }
    }

    /**
     * 获取推广汇总信息
     * get_pool
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * author MengShuai <133814250@qq.com>
     * date 2021/01/16 23:41
     *
     * @RequestMapping(path="get_pool")
     */
    public function get_pool()
    {
        $currUser       = $this->auth->check();
        $promotion_link = env('API_HOME_SHARE',
                '') . '/#/pages/spread/index?r=/home/spread/pool&job_number=' . $currUser['job_number'];
        $res            = $this->OfficialAccount->shorten($promotion_link);
        if (isset($res['errcode']) && $res['errcode'] === 0) {
            $promotion_link = $res['short_url'];
        }
        return $this->success([
            'promotion_link' => $promotion_link,
            //TODO..
        ]);
    }

    /**
     * 生成产品推广链接
     * generate_sharing_link
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * author MengShuai <133814250@qq.com>
     * date 2021/01/16 23:41
     *
     * @RequestMapping(path="generate_sharing_link")
     */
    public function generate_sharing_link()
    {
        $reqParam = $this->request->all();
        $currUser = $this->auth->check();

        $promotion_link = env('API_HOME_SHARE', '') . "/#/pages/spread/index?r=/home/spread/product_show&sid={$reqParam['id']}&job_number={$currUser['job_number']}&channel={$reqParam['sale_channel']}";
        $res            = $this->OfficialAccount->shorten($promotion_link);
        if (isset($res['errcode']) && $res['errcode'] === 0) {
            $promotion_link = $res['short_url'];
        }
        return $this->success([
            'promotion_link' => $promotion_link,
        ]);
    }
}