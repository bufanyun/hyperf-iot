<?php

declare(strict_types=1);

namespace Core\Plugins;

use Hyperf\Di\Annotation\Inject;
use HyperfLibraries\Sms\Contract\SmsInterface;
use Hyperf\Utils\ApplicationContext;
use App\Models\Ems as EmsModel;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\EventDispatcher\EventDispatcherInterface;
use App\Event\EmsEvent;

/**
 * 邮箱发送/验证类
 * Class Ems
 * @package Core\Plugins
 * author MengShuai <133814250@qq.com>
 * date 2021/01/14 10:08
 *
 * @property EmsModel $EmsModel
 * @property EventDispatcherInterface $eventDispatcher
 */
class Ems
{

    /**
     * @Inject()
     * @var EmsModel
     */
    protected $EmsModel;

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
     * 发送邮件
     * $event不为空时默认为验证码类，自动生成4位验证码
     * send
     * @param array $content
     * @param string|null $event
     * @return bool
     * author MengShuai <133814250@qq.com>
     * date 2021/01/14 11:56
     */
    public function send(array $content, string $event = 'html')
    {

        if($event !== 'html'){
            $code    = mt_rand(1000, 9999);
            $content['MsgHTML'] =  "您的验证码为：{$code}，有效期10分钟。" . ($content['MsgHTML'] !=='' ? '<hr>' : '') . $content['MsgHTML'];
        }
        $config = config('mailbox');
        $mail = ApplicationContext::getContainer()->get(PHPMailer::class);
        $mail->Hostname = '127.0.0.1'; //解决hyperf超全局变量关闭问题
        $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP(); // 设定使用SMTP服务
        $mail->SMTPDebug = 0; // 关闭SMTP调试功能
        $mail->SMTPAuth = true; // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl'; // 使用安全协议
        $mail->Host = $config['host']; // SMTP 服务器
        $mail->Port = $config['port']; // SMTP服务器的端口号
        $mail->Username = $config['username']; // SMTP服务器用户名
        $mail->Password = $config['password']; // SMTP服务器密码
        $mail->SetFrom($config['from'], $config['fromName']); // 邮箱，昵称
        $mail->Subject = $content['Subject'];
        $mail->MsgHTML($content['MsgHTML']);
        $mail->AddAddress($content['AddAddress']); // 收件人
        $result = $mail->Send();
        if (!$result) {
            return false;
        }
        if($event !== 'html') {
            $insert = [
                'event'      => $event,
                'mobile'     => $content['AddAddress'],
                'code'       => $code,
                'ip'         => getClientIp(),
                'created_at' => date("Y-m-d H:i:s"),
            ];
            $this->eventDispatcher->dispatch(new EmsEvent($insert, 'send'));
        }
        return true;
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
        $sms  = $this->EmsModel::query()->where(['mobile' => $mobile, 'event' => $event])
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
     * @return  Ems
     */
    public function get($mobile, $event = 'default')
    {
        $sms = $this->EmsModel::query()->where(['mobile' => $mobile, 'event' => $event])
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
        $count = $this->EmsModel::query()->where(['ip' => $ip])
            ->whereTime('created_at', '-1 hours')
            ->count();
        return $count ?? 0;
    }

}