<?php

declare(strict_types=1);

namespace Core\Plugins;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\Auth;
use Core\Services\AttachmentService;
use Hyperf\Di\Annotation\Inject;
use HyperfLibraries\Sms\Contract\SmsInterface;
use Hyperf\Utils\ApplicationContext;
use App\Models\Sms as SmsModel;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use HyperfExt\Mail\Mailable;

/**
 * 邮箱发送/验证类
 * Class Ems
 * @package Core\Plugins
 * author MengShuai <133814250@qq.com>
 * date 2021/01/14 10:08
 *
 * @property Mail $Mail
 */
class Ems extends Mailable
{


    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * 验证码有效时长
     * @var int
     */
    protected static $expire = 300;

    /**
     * 最大允许检测的次数
     * @var int
     */
    protected static $maxCheckNums = 10;

    public string $centent = '';

    public function setCentent(string $centent = '')
    {
        $this->centent = $centent;
        return $this->centent;
    }

    /**
     * 发送验证码
     *
     * @param int $mobile 手机号
     * @param int $code 验证码,为空时将自动生成4位数字
     * @param string $event 事件
     * @return  boolean
     */
    public function send($mobile, $code = null, $event = 'default')
    {

//        $easySms = ApplicationContext::getContainer()->get(SmsInterface::class);
//        $code    = is_null($code) ? mt_rand(1000, 9999) : $code;
//        $insert  = [
//            'event'      => $event,
//            'mobile'     => $mobile,
//            'code'       => $code,
//            'ip'         => getClientIp(),
//            'created_at' => date("Y-m-d H:i:s"),
//        ];
//
//        $result = true;
//        try {
//            $easySms->send($mobile, [
//                'template' => $this->getTemplate($event),
//                'data'     => [
//                    'code' => $code,
//                ],
//            ]);
//        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
//            $error  = $exception->getException('aliyun')->getMessage();
//            $logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get('send', 'sms');
//            $logger->error($error . "：" . json_encode($insert));
//            var_export($error);
//            $result = false;
//        }
//        if (!$result) {
//            return false;
//        }
//        $this->eventDispatcher->dispatch(new SmsEvent($insert, 'send'));
//        return true;
    }

    /**
     * 校验验证码
     *
     * @param int $mobile 手机号
     * @param int $code 验证码
     * @param string $event 事件
     * @return  boolean
     */
    public function check($mobile, $code, $event = 'default')
    {
        $time = time() - self::$expire;
        $sms  = $this->SmsModel::query()->where(['mobile' => $mobile, 'event' => $event])
            ->orderBy('id', 'desc')
            ->first();

        if ($sms) {
            if (strtotime((string)$sms->created_at) > $time && $sms->times <= self::$maxCheckNums) {
                $correct = $code == $sms->code;
                if (!$correct) {
                    $sms->times = $sms->times + 1;
                    $sms->save();
                    return false;
                } else {
                    return true;
                }
            } else {
                // 过期则清空该手机验证码
                $sms->delete();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取最后一次手机发送的数据
     *
     * @param int $mobile 手机号
     * @param string $event 事件
     * @return  Sms
     */
    public function get($mobile, $event = 'default')
    {
        $sms = $this->SmsModel::query()->where(['mobile' => $mobile, 'event' => $event])
            ->orderBy('id', 'desc')
            ->first();
        return $sms ? $sms : null;
    }

    /**
     * 获取ip在一小时内发送累计次数
     * getIpFrequency
     * @param string $ip
     * @return int
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 09:31
     */
    public function getIpFrequency(string $ip) : int
    {
        $count = $this->SmsModel::query()->where(['ip' => $ip])
            ->whereTime('created_at', '-1 hours')
            ->count();
        return $count ?? 0;
    }

    /**
     * 获取短信模板
     * getTemplate
     * @param string $event
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2021/01/13 15:57
     */
    public function getTemplate(string $event = 'default')
    {
        //暂时统一使用阿里
        return env("ALIYUN_" . strtoupper($event) . "_TEMPLATE", null);
    }
}