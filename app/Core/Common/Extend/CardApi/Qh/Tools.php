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
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 11:32
     */
    public function uniform(array $inputParam) : array
    {
        $params = [$inputParam];
            $res = $this->request($this->config['domain'] . 'order_submit.php', $params);
            $res = strstr($res, '{'); //因为返回结果可能是jsonp，所以删除花括号前的所以字符，便于转数组
            $res = json_decode($res, true);
            return $res;
//            if($res['rspCode'] == 2000 || $res['rspDesc'] == '提交成功'){
//
//            }
//
//            return json(['rspCode' => $res['rspCode'], 'rspDesc' => $res['rspDesc']]);
    }


    /**
     * 转发选号接口
     * selectPhones
     * @param string $cuccProvinceEcss
     * @param string $cuccCityEcss
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2021/01/11 11:41
     */
    public function selectPhones(string $cuccProvinceEcss,string $cuccCityEcss) : array
    {
        $params = ['cuccProvinceEcss' => $cuccProvinceEcss, 'cuccCityEcss' => $cuccCityEcss];
        $res = $this->request($this->config['domain'] . 'xuanhao.php', $params);
        return json_decode($res,true);
    }

    /**
     * 统一返回格式
     * format
     * @param string $rs
     * @param array  $keys
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 23:20
     */
    public function format(string $rs, array $keys): array
    {
        $rs   = json_decode($rs, true);
        $data = [];
        if (!empty($keys)) {
            foreach ($rs as $k => $v) {
                if (in_array($k, $keys)) {
                    $data[$k] = $v;
                }
            }
        }
        $res = [
            'code' => (isset($rs['result']) && $rs['result'] == true) ? StatusCode::SUCCESS : StatusCode::ERR_EXCEPTION,
            'msg'  => isset($rs['msg']) ? (string)$rs['msg'] : '',
            'data' => $data,
        ];
        if ($res['msg'] == '' && isset($rs['error'])) {
            $res['msg'] = (string)$rs['error'];
        }

        return $res;
    }

    /**
     * 发送请求
     * request
     * @param string $method
     * @param array  $data
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 23:20
     */
    public function request(string $url, array $data): string
    {
        var_export([$url, $data]);
        return $this->Curl->curl_post($url, $data); //协程
    }


}