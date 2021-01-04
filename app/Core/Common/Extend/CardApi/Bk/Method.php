<?php

declare(strict_types=1);
namespace Core\Common\Extend\CardApi\Bk;

/**
 * 北滘联通 - 接口方法整合
 * Class Method
 * @package extend\UniversalPush
 */
class Method
{

    public static $lists = [
        'selectPhones' => ['/threepartyorderdata/selectPhones', ['flexData']], //选号
        'ZOPsubmit' => ['/threepartyorderdata/ZOPsubmit', ['order']], //统一下单
        'getCode' => ['/threepartyorderdata/getCode', []], //获取验证码
        'messageCheck' => ['/threepartyorderdata/messageCheck', []], //效验验证码
        'GetOrders' => ['/threepartyorderdata/GetOrders', ['data']], //订单明细查询
        'activeMsg' => ['/PingAnHealthy/activeData/activeMsg', ['flexData']], //号码激活在网状态查询接口
    ];

    public static function get(string $method)
    {
        return isset(static::$lists[$method]) ? static::$lists[$method] : '';
    }
}