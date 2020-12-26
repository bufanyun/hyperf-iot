<?php

namespace Core\Common\Extend\CardApi\Bk;

use Core\Common\Extend\CardApi\Bk\Method;
use Core\Common\Extend\CardApi\Bk\Aes;
use Core\Common\Extend\CardApi\Bk\districtInspection;
use Core\Common\Extend\Tools\Curl;
use Hyperf\Di\Annotation\Inject;
use App\Constants\StatusCode;

/**
 * 北滘联通 - 工具类
 * Class Tools
 * @package extend\Bk
 */
class Tools
{
    /**
     *
     * @Inject()
     * @var districtInspection
     */
    private $districtInspection;

    public $config = [
        'appID' => '',
        'AES' => '',
        'domain' => 'https://fsuni.com',  //统一请求接口
    ];

    public function __construct()
    {
        $this->config = array_merge($this->config, [
            'appID' => env('BK_CONFIG_APPID', ''),
            'AES' => env('BK_CONFIG_AES', ''),
            'development_code' => env('BK_CONFIG_DEVELOPMENT_CODE', ''),
        ]);
    }

    /**
     * 统一返回格式
     * @param string $rs
     * @param array $keys
     * @return array
     */
    public function format(string $rs, array $keys): array
    {
        $rs = json_decode($rs, true);
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
            'msg' => isset($rs['msg']) ? (string)$rs['msg'] : '',
            'data' => $data,
        ];
        if ($res['msg'] == '' && isset($rs['error'])) {
            $res['msg'] = (string)$rs['error'];
        }
        return $res;
    }

    /**
     * 发送请求
     * @param string $method
     * @param array $data
     * @return array
     */
    public function request(string $method, array $data): array
    {
        if (($method = Method::get($method)) == '') {
            return ['success' => false, 'code' => 90, 'message' => 'method不存在，请检查', 'data' => null];
        }
        $curl = make(Curl::class, [2]);
        $params = $this->buildParams($data);
//       var_export([$params,'url' => $this->config['domain'] . $method[0]]);
        $rs = $curl->post($this->config['domain'] . $method[0], $params, 8000);
        return $this->format($rs, $method[1]);
    }

    /**
     * 生成请求参数
     * buildParams
     * @param array $data
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/11/24 17:00
     */
    public function buildParams(array $data): ?array
    {
        return array_merge($data, [
            'appID' => $this->config['appID'],
            'token' => $this->getToken(),
        ]);
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken(): string
    {
        $data = $this->getMillisecond() . "|" . $this->config['appID'];
        $encrypt = make(Aes::class, [$this->config['AES']])->encrypt($data);
        return $encrypt;
    }

    /**
     * 获取13位时间戳
     * getMillisecond
     * @return float
     * author MengShuai <133814250@qq.com>
     * date 2020/11/24 16:46
     */
    public function getMillisecond(): float
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    public function getAscription(string $province, string $city) :? array
    {
        return $this->districtInspection->getAscription($province, $city);
    }

    public function getArea(string $province, string $city, string $district) :? array
    {
        return $this->districtInspection->getArea($province, $city, $district);
    }

    public function getAscriptionCode(int $province, int $city) :? array
    {
        return $this->districtInspection->getAscriptionCode($province, $city);
    }

    public function getAreaCode(int $province, int $city, int $district) :? array
    {
        return $this->districtInspection->getAreaCode($province, $city, $district);
    }
}