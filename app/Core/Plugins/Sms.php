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
use App\Event\SmsEvent;

/**
 * 短信发送/验证类
 * Class Sms
 * @package Core\Plugins
 * author MengShuai <133814250@qq.com>
 * date 2021/01/13 14:02
 *
 * @property SmsModel $SmsModel
 */
class Sms
{
    /**
     * @Inject
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @Inject()
     * @var SmsModel
     */
    protected $SmsModel;

    /**
     * 验证码有效时长
     * @var int
     */
    protected static $expire = 120;

    /**
     * 最大允许检测的次数
     * @var int
     */
    protected static $maxCheckNums = 10;

    /**
     * 获取最后一次手机发送的数据
     *
     * @param   int    $mobile 手机号
     * @param   string $event  事件
     * @return  Sms
     */
    public static function get($mobile, $event = 'default')
    {
        $sms = \app\common\model\Sms::
        where(['mobile' => $mobile, 'event' => $event])
            ->order('id', 'DESC')
            ->find();
        Hook::listen('sms_get', $sms, null, true);
        return $sms ? $sms : null;
    }

    private function getAliTemplate($event = 'default')
    {
        return env("ALIYUN_".strtoupper($event)."_TEMPLATE", '');
    }

    /**
     * 发送验证码
     *
     * @param   int    $mobile 手机号
     * @param   int    $code   验证码,为空时将自动生成4位数字
     * @param   string $event  事件
     * @return  boolean
     */
    public function send($mobile, $code = null, $event = 'default')
    {
        $easySms = ApplicationContext::getContainer()->get(SmsInterface::class);
        $code = is_null($code) ? mt_rand(1000, 9999) : $code;
        $insert = [
            'event' => $event,
            'mobile' => $mobile,
            'code' => $code,
            'ip' => getClientIp(),
            'created_at' => date("Y-m-d H:i:s")
        ];
//        $sms = $this->SmsModel->query()->insert($insert);
        $result = true;
//        try {
//            $easySms->send($mobile, [
////            'content' => '{1}为您的登录验证码，请于5分钟内填写',
//                'template' => $this->getAliTemplate($event),
//                'data' => [
//                    'code' => $code
//                ],
//            ]);
//        }catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
//            var_export($exception->getException('aliyun')->getMessage());
//            $logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get('send','sms');
//            $logger->error($exception->getException('aliyun')->getMessage() . " " . json_encode($insert));
//            $result = false;
//        }
//
//        $result = Hook::listen('sms_send', $sms, null, true);
        if (!$result) {
            return false;
        }
       $rs = $this->eventDispatcher->dispatch(new SmsEvent($insert));

        var_export(['$rs' =>$rs->sms]);
        return true;
    }

    /**
     * 发送通知
     *
     * @param   mixed  $mobile   手机号,多个以,分隔
     * @param   string $msg      消息内容
     * @param   string $template 消息模板
     * @return  boolean
     */
    public static function notice($mobile, $msg = '', $template = null)
    {
        $params = [
            'mobile'   => $mobile,
            'msg'      => $msg,
            'template' => $template
        ];
        $result = Hook::listen('sms_notice', $params, null, true);
        return $result ? true : false;
    }

    /**
     * 校验验证码
     *
     * @param   int    $mobile 手机号
     * @param   int    $code   验证码
     * @param   string $event  事件
     * @return  boolean
     */
    public static function check($mobile, $code, $event = 'default')
    {
        $time = time() - self::$expire;
        $sms = \app\common\model\Sms::where(['mobile' => $mobile, 'event' => $event])
            ->order('id', 'DESC')
            ->find();
        if ($sms) {
            if ($sms['createtime'] > $time && $sms['times'] <= self::$maxCheckNums) {
                $correct = $code == $sms['code'];
                if (!$correct) {
                    $sms->times = $sms->times + 1;
                    $sms->save();
                    return false;
                } else {
                    $result = Hook::listen('sms_check', $sms, null, true);
                    return $result;
                }
            } else {
                // 过期则清空该手机验证码
                self::flush($mobile, $event);
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 清空指定手机号验证码
     *
     * @param   int    $mobile 手机号
     * @param   string $event  事件
     * @return  boolean
     */
    public static function flush($mobile, $event = 'default')
    {
        \app\common\model\Sms::
        where(['mobile' => $mobile, 'event' => $event])
            ->delete();
        Hook::listen('sms_flush');
        return true;
    }

}