<?php

declare(strict_types=1);

namespace Core\Common\Extend\CardApi\Qh;

use Core\Common\Extend\Helpers\CurlHelpers;
use Hyperf\Di\Annotation\Inject;
use App\Constants\StatusCode;
use Hyperf\Utils\ApplicationContext;

/**
 * 抢号 - 工具类
 * Class Tools
 *
 * @package extend\Qh
 */
class Tools
{

    private ?CurlHelpers $Curl = null;

    public $config = [
        'domain' => 'https://mi.qiangka.com/dianxin_c1/',  //统一请求接口
    ];

    public function __construct()
    {
        $this->Curl = (ApplicationContext::getContainer())->get(CurlHelpers::class);
    }

    /**
     * 提交订单
     * uniform
     * @param array $inputParam
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 17:40
     */
    public function uniform(array $inputParam): array
    {
        $res = $this->request($this->config['domain'] . 'order_submit.php', $inputParam);
        $res = strstr($res, '{'); //因为返回结果可能是jsonp，所以删除花括号前的所以字符，便于转数组
        $res = json_decode($res, true);
        return $res;
    }

    /**
     * 转发选号接口
     * selectPhones
     * @param string $cuccProvinceEcss
     * @param string $cuccCityEcss
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 17:40
     */
    public function selectPhones(string $cuccProvinceEcss, string $cuccCityEcss): array
    {
        $params = ['cuccProvinceEcss' => $cuccProvinceEcss, 'cuccCityEcss' => $cuccCityEcss];
        $res    = $this->request($this->config['domain'] . 'xuanhao.php', $params);
        return json_decode($res, true);
    }

    /**
     * 发送请求
     * request
     * @param string $url
     * @param array $data
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 17:39
     */
    public function request(string $url, array $data): string
    {
        return $this->Curl->curl_post($url, $data, ["Content-type: application/json"]); //协程
    }

}