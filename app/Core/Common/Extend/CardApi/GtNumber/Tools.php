<?php

declare(strict_types=1);
namespace Core\Common\Extend\CardApi\GtNumber;

use Core\Common\Extend\Helpers\CurlHelpers;
use App\Constants\StatusCode;
use Hyperf\Utils\ApplicationContext;

/**感叹号 - 工具类
 * Class Tools
 *
 * @package extend\Bk
 */
class Tools
{
    private array $config = [];

    private ?CurlHelpers $Curl = null;

    public function __construct()
    {
        $this->config = [
            'share_id'   => env('GT_NUMBER_SHARE_ID'),
            'api_domian' => env('GT_NUMBER_PLACE_ORDER_URL'),  //下单
            'api_chadan' => env('GT_NUMBER_CHECK_LIST_URL'),  //订单查询
        ];
        $this->Curl   = (ApplicationContext::getContainer())->get(CurlHelpers::class);
    }

    /**
     * 统一订单提交
     * uniform
     *
     * @param array $data
     *
     * @return array|bool|string
     * author MengShuai <133814250@qq.com>
     * date 2020/11/24 22:30
     */
    public function uniform(array $data): array
    {
        $result = $this->getinfo($data['mobile'], $data['sku']);
        if ($result !== true) {
            return [
                'code' => StatusCode::ERR_EXCEPTION,
                'msg'  => $result,
                'data' => [],
            ];
        }
        $arr    = $data + [
                'share_id'      => $this->config['share_id'],  //推广者id
                'province_code' => '110000', //这里瞎填的 不知道会不会影响
                'city_code'     => '110100',
                'district_code' => '110101',
                'pretty_number' => '',
                'source'        => '',
            ];
        $result = $this->Curl->curl_post($this->config['api_domian'], $arr); //协程
        $result = $this->format($result);
        if ($result['code'] == StatusCode::SUCCESS) {
            //TODO
            //..订单成功额外处理
        }

        return $result;
    }

    /**
     * 拉取下单手机号订单详细
     * getInfo
     *
     * @param string $mobile
     *
     * @return bool|string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 23:17
     */
    public function getInfo(string $mobile, string $sku)
    {
        if (isMobile($mobile) === false) {
            return '手机号格式不正确';
        }
        $url    = $this->config['api_chadan'] . $mobile;
        $result = $this->Curl->curl_get($url);
        $result = $this->format($result);
        if (isset($result['data']) && count($result['data']) > 0) {
            static $status = ['开卡失败', '订单终止', '已退货', '证件不合格待重传'];
            foreach ($result['data'] as $vo){
                if($sku !== $vo['product_sku']){
                    continue;
                }
                if (in_array($vo['status'], $status)) {
                    continue;
                }
                unset($vo);
                return '一人限领一张，已超出上限！';
            }
            unset($vo);
        }

        return true;
    }

    /**
     * 格式化返回结果
     * format
     *
     * @param string $result
     *
     * @return array
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 23:17
     */
    private function format(string $result): array
    {
        $result = json_decode($result, true);
        if (!isset($result['msg'])) {
            return ['ret' => StatusCode::ERR_EXCEPTION, 'msg' => '数据解析失败', 'data' => []];
        }
        if (isset($result['msg']['code']) && $result['msg']['code'] == 0) {
            $res = [
                'code' => StatusCode::SUCCESS,
                'msg'  => (isset($result['info']) && $result['info'] != '') ? $result['info'] : '获取成功',
                'data' => $result['data'],
            ];
        } else {
            $res = [
                'code' => StatusCode::ERR_EXCEPTION,
                'msg'  => isset($result['info']) ? $result['info'] : '',
                'data' => $result['data'],
            ];
        }

        return $res;
    }
}