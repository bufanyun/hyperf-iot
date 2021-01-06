<?php

declare(strict_types=1);

namespace Core\Repositories\Home;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Constants\ProductOrderCode;
use App\Exception\BusinessException;
use Core\Repositories\BaseRepository;
use http\Exception\BadConversionException;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Core\Common\Extend\CardApi\Bk\Tools as BkApi;
use Core\Common\Extend\CardApi\GtNumber\Tools as GtApi;
use Core\Common\Container\Redis;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Core\Repositories\Common\Bufan\ImplRepository;

/**
 * orderSubmit
 * 订单提交
 *
 * @package Core\Repositories\Home
 *
 * @property \Psr\Log\LoggerInterface $logger
 * @property \Core\Common\Container\Redis $Redis
 * @property \Core\Common\Extend\CardApi\Bk\Tools $BkApi
 * @property \Core\Common\Extend\CardApi\GtNumber\Tools $GtApi
 * @property \Core\Repositories\Common\Bufan\ImplRepository $ImplRepository
 */
class OrderSubmitRepository extends BaseRepository
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var Redis
     */
    private $Redis;

    /**
     * @var BkApi
     */
    private $BkApi;

    /**
     * @var GtApi
     */
    private $GtApi;

    /**
     * @var ImplRepository
     */
    private $ImplRepository;

    public function __construct()
    {
        $Container            = ApplicationContext::getContainer();
        $this->BkApi          = $Container->get(BkApi::class);
        $this->GtApi          = $Container->get(GtApi::class);
        $this->Redis          = $Container->get(Redis::class);
        $this->ImplRepository = $Container->get(ImplRepository::class);
        $this->logger         = $Container->get(LoggerFactory::class)->get(__CLASS__);
    }

    /**
     * 默认模板
     * default
     *
     * @param $inputData
     *
     * @return mixed
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 15:41
     */
    public function default($inputData)
    {
        $product = Db::table('product_sale')
            ->select('product_access.*', 'product_sale.*')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where([
                'product_sale.status' => 1,
                'product_sale.id'     => $inputData['sid'],
            ])
            ->first();
        if ($product == null) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '商品不存在或已停售');
        }
//        var_export($inputData);
        switch ($product->api_model) {
            case 'BkApi':
                if ($product->captcha_switch) {
                    if (!isset($inputData['captchaInfo']['captcha'])) {
                        throw new BusinessException(StatusCode::ERR_EXCEPTION,
                            '验证码错误');
                    }
                    // TODO
                    //...
                }
                if ((int)$inputData['numInfo']['essProvince'] === 51) {
                    $inputData['numInfo']['essCity'] = 530; //凡是有号码归属地市为广东省的，需要将地市选为广东、佛山市
                }
                $Ascription = $this->BkApi->getAscriptionCode(
                    (int)$inputData['numInfo']['essProvince'],
                    (int)$inputData['numInfo']['essCity']
                );
                if (!$Ascription) {
                    return $this->error(StatusCode::ERR_EXCEPTION,
                        '获取接口中归属地信息失败，请联系管理员处理');
                }

                $Area = $this->BkApi->getAreaCode(
                    (int)$inputData['postInfo']['webProvince'],
                    (int)$inputData['postInfo']['webCity'],
                    (int)$inputData['postInfo']['webCounty']
                );
                if (!$Area) {
                    return $this->error(StatusCode::ERR_EXCEPTION,
                        '获取接口中收货地信息失败，请联系管理员处理');
                }

                $admin_id         = Db::table('user')
                    ->where(['job_number' => $inputData['job_number']])
                    ->value('id');
                $validation       = [
                    'admin_id'     => $admin_id ? $admin_id : env('SUPER_ADMIN', ''),
                    'name'         => $inputData['certInfo']['certName'],
                    'sim_identity' => $inputData['certInfo']['certId'],
                    'phone'        => $inputData['certInfo']['contractPhone'],
                    'province'     => $Area['province_name'],
                    'city'         => $Area['city_name'],
                    'district'     => $Area['district_name'],
                    'address'      => $inputData['postInfo']['address'],
                ];
                $ValidationAccess =
                    $this->ValidationAccess($validation, $product);
                if ($ValidationAccess !== true) {
                    throw new BusinessException(StatusCode::ERR_EXCEPTION,
                        $ValidationAccess);
                }

                $data = [
                    'name'             => $inputData['certInfo']['certName'],
                    'identity'         => $inputData['certInfo']['certId'],
                    'contact'          => $inputData['certInfo']['contractPhone'],
                    'ship_province'    => $Area['province_name'],
                    'ship_city'        => $Area['city_name'],
                    'ship_country'     => $Area['district_name'],
                    'ship_addr'        => $inputData['postInfo']['address'],
                    'province'         => $Ascription['province_name'],
                    'city'             => $Ascription['city_name'],
                    'newnumber'        => $inputData['numInfo']['number'],
                    'development_code' => $this->BkApi->config['development_code'],
                    'productCode'      => $product->kind,
                    //                    'captchaId'        => $inputData['captchaInfo']['captcha'],
                ];
                var_export($data);
                $res = $this->BkApi->request('ZOPsubmit', $data);
                var_export(['$res' => $res]);
                if ($res['code'] !== StatusCode::SUCCESS) {
                    throw new BusinessException(StatusCode::ERR_EXCEPTION, $res['msg']);
                }
//                $res = [
//                    'code' => 20000,
//                    'msg'  => '下单成功！',
//                    'data' =>
//                        [
//                            'order' =>
//                                [
//                                    'order_no'         => uniqid(),
//                                    'productCode'      => 'DW_NO_PRIZES_CARD',
//                                    'create_time'      => '2020-12-25 22:11:23',
//                                    'province'         => '江苏',
//                                    'city'             => '南京市',
//                                    'newnumber'        => '13042568502',
//                                    'development_code' => '5112191792',
//                                    'order_id'         => uniqid(),
//                                ],
//                        ],
//                ];

                $insert = $validation + [
                        'sid'            => $inputData['sid'],
                        'dock_order_id'  => $res['data']['order']['order_id'],
                        'order_id'       => $this->GeneratOrderNumber(),
                        'app_province'   => $Ascription['province_name'],
                        'app_city'       => $Ascription['city_name'],
                        'app_number'     => $inputData['numInfo']['number'],
                        'sale_channel'   => isset($inputData['sale_channel']) ? $inputData['sale_channel'] : 0,
                        'source'         => isset($inputData['source']) ? $inputData['source'] : '',
                        'created_at'     => date("Y-m-d H:i:s"),
                        'status'         => ProductOrderCode::STATUS_TO_EXAMINE,
                        'activat_status' => ProductOrderCode::ACTIVAT_STATUS_NOT,
                        'pay_status'     => ProductOrderCode::PAY_STATUS_SUCCESSFUL,
                    ];
                $this->createOrder($insert);
                $this->ImplRepository->templateBkApi($inputData, $Ascription, $Area, $product);
                return [
                    'code' => StatusCode::SUCCESS,
                    'msg'  => '提交成功',
                    'data' => [
                        'order_id' => $insert['order_id'],
                    ],
                ];
            case "GtApi":
                $Area = $this->BkApi->getAreaCode(
                    (int)$inputData['postInfo']['webProvince'],
                    (int)$inputData['postInfo']['webCity'],
                    (int)$inputData['postInfo']['webCounty']
                );
                if (!$Area) {
                    return $this->error(StatusCode::ERR_EXCEPTION,
                        '获取接口中收货地信息失败，请联系管理员处理');
                }

                $admin_id         = Db::table('user')
                    ->where(['job_number' => $inputData['job_number']])
                    ->value('id');
                $validation       = [
                    'admin_id'     => $admin_id ? $admin_id : env('SUPER_ADMIN', ''),
                    'name'         => $inputData['certInfo']['certName'],
                    'sim_identity' => $inputData['certInfo']['certId'],
                    'phone'        => $inputData['certInfo']['contractPhone'],
                    'province'     => $Area['province_name'],
                    'city'         => $Area['city_name'],
                    'district'     => $Area['district_name'],
                    'address'      => $inputData['postInfo']['address'],
                ];
                $ValidationAccess =
                    $this->ValidationAccess($validation, $product);
                if ($ValidationAccess !== true) {
                    throw new BusinessException(StatusCode::ERR_EXCEPTION,
                        $ValidationAccess);
                }

                //生成下单信息
                $source_id = 'WEBAPP' . time() . str_rand(6);
                $data      = [
                    'sku'       => $product->kind,  //商品名称
                    'source_id' => $source_id, //api订单号，唯一标识
                    'id_name'   => $inputData['certInfo']['certName'],  //真实姓名
                    'id_num'    => $inputData['certInfo']['certId'],    //身份证号
                    'name'      => $inputData['certInfo']['certName'], //收货人
                    'mobile'    => $inputData['certInfo']['contractPhone'], //下单手机号
                    'province'  => $Area['province_name'], //收货省
                    'city'      => $Area['city_name'], //收货市
                    'district'  => $Area['district_name'],  //收货区域、县
                    'address'   => $inputData['postInfo']['address'],  //详细收货地址、街道
                ];

                $res = $this->GtApi->uniform($data);
                if ($res['code'] !== StatusCode::SUCCESS) {
                    throw new BusinessException(StatusCode::ERR_EXCEPTION, $res['msg']);
                }
                $insert = $validation + [
                        'sid'            => $inputData['sid'],
                        'dock_order_id'  => $source_id,
                        'order_id'       => $this->GeneratOrderNumber(),
                        'sale_channel'   => isset($inputData['sale_channel']) ? $inputData['sale_channel'] : 0,
                        'source'         => isset($inputData['source']) ? $inputData['source'] : '',
                        'created_at'     => date("Y-m-d H:i:s"),
                        'status'         => ProductOrderCode::STATUS_TO_EXAMINE,
                        'activat_status' => ProductOrderCode::ACTIVAT_STATUS_NOT,
                        'pay_status'     => ProductOrderCode::PAY_STATUS_SUCCESSFUL,
                    ];
                $this->createOrder($insert);
                $this->ImplRepository->templateGtApi($inputData, $Area, $product);
                return [
                    'code' => StatusCode::SUCCESS,
                    'msg'  => '提交成功',
                    'data' => [
                        'order_id' => $insert['order_id'],
                    ],
                ];
            default:
                throw new BusinessException(StatusCode::ERR_EXCEPTION,
                    '未开放模块');
        }
    }

    /**
     * 创建本地订单
     * createOrder
     *
     * @param array $insert
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 09:13
     */
    private function createOrder(array $insert): void
    {
        Db::beginTransaction();
        try {
            $res = Db::table('product_order')->insert($insert);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            $this->logger->info('订单插入失败,' . __LINE__ . '行：' . json_encode($insert,
                    JSON_UNESCAPED_UNICODE) . "\r\n 错误提示：" . $ex->getMessage());
            throw new BusinessException(StatusCode::ERR_EXCEPTION,
                $ex->getMessage());
        }
        if (!$res) {
            Db::rollBack();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,
                '订单提交失败，稍后再试！');
        }
    }

    /**
     * 生成唯一订单号
     * GeneratOrderNumber
     *
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 09:25
     */
    private function GeneratOrderNumber(): string
    {
        $order_id = date("YmdHi") . uniqid();
        if (Db::table('product_order')->where('order_id', $order_id)->exists()) {
            return $this->GeneratOrderNumber();
        }
        return $order_id;
    }

    /**
     * 模型规则检查
     * ValidationAccess
     *
     * @param $validation
     * @param $product
     *
     * @return bool|string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/25 18:03
     */
    private function ValidationAccess($validation, $product)
    {
//        var_export($product);
        if ($product->age_limit !== 'null') {
            $product->age_limit = json_decode($product->age_limit, true);
            $age                = getIdCardAge((string)$validation['sim_identity']);
            if ($age < $product->age_limit[0] || $age > $product->age_limit[1]) {
                return '年龄需在' . $product->age_limit[0] . '至' . $product->age_limit[1]
                    . '才能申请！' . $age . '--' . (string)$validation['sim_identity'];
            }
        }
        if ($product->pay_limit !== 'null') {
            $product->pay_limit = json_decode($product->pay_limit, true);
            //TODO  下单数量限制
        }
        if ($product->stocks < 1) {
            return $product->name . '太火爆啦，已经没有库存啦，联系客服试试吧~';
        }

        //TODO
        //后续增加禁区、代理商提货权限效验

        return true;
    }

}