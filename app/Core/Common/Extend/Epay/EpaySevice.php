<?php
declare(strict_types=1);

namespace Core\Common\Extend\Epay;

use Core\Common\Extend\Epay\AlipaySubmit;
use Core\Common\Extend\Epay\AlipayNotify;

class EpaySevice
{

    protected $epay_config;

    private ? AlipaySubmit $AlipaySubmit = null;

    private ? AlipayNotify $AlipayNotify = null;

    public function __construct()
    {
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        $this->epay_config  = [
            'partner'       => trim(env('EPAY_CONFIG_PARTNER', '')),
            'key'           => env('EPAY_CONFIG_KEY', ''),
            'sign_type'     => strtoupper('MD5'),
            'input_charset' => strtolower('utf-8'),
            'transport'     => !isHTTPS() ? 'http' : 'https',
            'apiurl'        => env('EPAY_CONFIG_APIURL', ''),
        ];
        $this->AlipaySubmit = make(AlipaySubmit::class, [$this->epay_config]);
        $this->AlipayNotify = make(AlipayNotify::class, [$this->epay_config]);
    }

    /**
     *
     * 发起订单退款
     *
     * author [MengShuai] [<133814250@qq.com>]
     */
    public function refunnd($data = [])
    {
        $params = [
            'act'      => 'refunnd',
            'pid'      => $this->epay_config['partner'],
            'key'      => $this->epay_config['key'],
            'refund'   => $data['refund'],
            'trade_no' => $data['trade_no'],
        ];
        $result = Http::get($this->epay_config['apiurl'] . 'api.php', $params);
        $result = json_decode($result, true);

//        var_export($this->epay_config['apiurl'] . 'api.php');exit;
        /**
         * 异常处理+邮件通知
         */
        if (!isset($result['code']) || !isset($result['msg'])) {
            \app\common\library\Ems::notice(
                Config::get('site')['admin_mailbox'],
                Config::get('site')['name'] . ' - 订单退款API通讯异常通知',
                $msg = '主人，发现一个订单退款API通讯异常，订单号：' . $data['trade_no'] . '，请尽快登录查看..');
            return ['code' => -20, 'msg' => '退款API通讯异常'];
        }

        return $result;
    }

    /**
     *
     * 跳转支付网关
     *
     * author [MengShuai] [<133814250@qq.com>]
     */
    public function purchase($data)
    {
        $parameter = [
            "pid"          => $this->epay_config['partner'],
            "type"         => $data['type'],
            'notify_url'   => $data['notify_url'],//统一异步通知地址
            'return_url'   => $data['return_url'],//跳转地址
            "out_trade_no" => $data['tradeno'], //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
            "name"         => $data['name'],  //商品名称
            "money"        => (float)$data['price'],
            "sitename"     => $data['sitename']  //支付titile
        ];

        //建立请求
        $html_text = $this->AlipaySubmit->buildRequestForm($parameter, "POST", "正在跳转");
        return $html_text;
    }


    /**
     *
     * 回调验证
     * author [MengShuai] [<133814250@qq.com>]
     */
    public function notify()
    {
        /**
         * 回调格式实例
         * "GET /payment/notify?money=0.01&name=%E5%85%85%E5%80%BC%E4%BD%99%E9%A2%9D&out_trade_no=1582299704UID1&pid=10001&trade_no=2020022123414550494&trade_status=TRADE_SUCCESS&type=wxpay&sign=ac49bbc792cf05046075b8b60223ceb9&sign_type=MD5 HTTP/1.1" 302 0 "-"
         * "GET /user/code?money=0.01&name=%E5%85%85%E5%80%BC%E4%BD%99%E9%A2%9D&out_trade_no=1582299704UID1&pid=10001&trade_no=2020022123414550494&trade_status=TRADE_SUCCESS&type=wxpay&sign=ac49bbc792cf05046075b8b60223ceb9&sign_type=MD5 HTTP/1.1" 200 4136 "http://pay.xianweicm.com/wxpay.php?trade_no=2020022123414550494&sitename=6Jy76JyTMi4w"
         */

        /**
         * 可能用到的数据
         * $money = (float)$_GET['money']; //实际付款金额
         * $pay_no = $_GET['trade_no']; //流水号
         * $trade_status = $_GET['trade_status']; //交易状态
         */

        //计算得出通知验证结果
        $verify_result = $this->AlipayNotify->verifyNotify();
        return !$verify_result ? false : true;
    }


    public function getReturnHTML($request, $response, $args)
    {
        // TODO: Implement getReturnHTML() method.
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }
}
