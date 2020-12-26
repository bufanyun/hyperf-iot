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
use Core\Common\Container\Redis;
use Hyperf\Logger\LoggerFactory;

/**
 * orderSubmit
 * 订单提交
 *
 * @package Core\Repositories\Home
 *
 * @property \Core\Services\AttachmentService $attachmentService
 */
class orderSubmitRepository extends BaseRepository
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

    public function __construct()
    {
        $this->BkApi = make(BkApi::class);
        $this->Redis = make(Redis::class);
        $this->logger = (make(LoggerFactory::class))->get(__CLASS__);
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
                    if ( ! isset($inputData['captchaInfo']['captcha'])) {
                        throw new BusinessException(StatusCode::ERR_EXCEPTION,
                            '验证码错误');
                    }
                    // TODO
                    //...
                }
                if((int)$inputData['numInfo']['essProvince'] === 51){
                    $inputData['numInfo']['essCity'] = 530; //凡是有号码归属地市为广东省的，需要将地市选为广东、佛山市
                }
                $Ascription = $this->BkApi->getAscriptionCode(
                    (int)$inputData['numInfo']['essProvince'],
                    (int)$inputData['numInfo']['essCity']
                );
                if ( ! $Ascription) {
                    return $this->error(StatusCode::ERR_EXCEPTION,
                        '获取接口中归属地信息失败，请联系管理员处理');
                }

                $Area = $this->BkApi->getAreaCode(
                    (int)$inputData['postInfo']['webProvince'],
                    (int)$inputData['postInfo']['webCity'],
                    (int)$inputData['postInfo']['webCounty']
                );
                if ( ! $Area) {
                    return $this->error(StatusCode::ERR_EXCEPTION,
                        '获取接口中收货地信息失败，请联系管理员处理');
                }

                $admin_id = Db::table('user')
                    ->where(['job_number' => $inputData['job_number']])
                    ->value('id');
                $validation = [
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
//                                var_export($data);
//                                $res = $this->BkApi->request('ZOPsubmit', $data);
//                                if($res['code'] !== StatusCode::SUCCESS){
//                                    throw new BusinessException(StatusCode::ERR_EXCEPTION, $res['msg']);
//                                }
                $res = [
                    'code' => 20000,
                    'msg'  => '下单成功！',
                    'data' =>
                        [
                            'order' =>
                                [
                                    'order_no'         => uniqid(),
                                    'productCode'      => 'DW_NO_PRIZES_CARD',
                                    'create_time'      => '2020-12-25 22:11:23',
                                    'province'         => '江苏',
                                    'city'             => '南京市',
                                    'newnumber'        => '13042568502',
                                    'development_code' => '5112191792',
                                    'order_id'         => uniqid(),
                                ],
                        ],
                ];

                $insert = $validation + [
                    'sid'            => $inputData['sid'],
                    'dock_order_id'  => $res['data']['order']['order_id'],
                    'order_id'       => $this->GeneratOrderNumber(),
                    'app_province'   => $Ascription['province_name'],
                    'app_city'       => $Ascription['city_name'],
                    'app_number'     => $inputData['numInfo']['number'],
                    'sale_channel'   => isset($inputData['sale_channel']) ? $inputData['sale_channel'] : '',
                    'created_at'     => date("Y-m-d H:i:s"),
                    'status'         => ProductOrderCode::STATUS_TO_EXAMINE,
                    'activat_status' => ProductOrderCode::ACTIVAT_STATUS_NOT,
                ];
                $this->createOrder($insert);
                return [
                    'code' => StatusCode::SUCCESS,
                    'msg'  => '提交成功',
                    'data' => [
                        'order_id' => $insert['order_id'],
                    ],
                ];
            case "str2":
                throw new BusinessException(StatusCode::ERR_EXCEPTION, '未开放模块');
            default:
                throw new BusinessException(StatusCode::ERR_EXCEPTION,
                    '未开放模块1');
        }
    }

    /**
     * 创建本地订单
     * createOrder
     * @param  array  $insert
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 09:13
     */
    private function createOrder(array $insert): void
    {
        Db::beginTransaction();
        try {
            var_export(['$insert' => $insert]);
            $res = Db::table('product_order')->insert($insert);
            Db::commit();
        } catch (\Throwable $ex) {
            Db::rollBack();
            $this->logger->info('订单插入失败,'.__LINE__.'行：'.json_encode($insert,
                    JSON_UNESCAPED_UNICODE)."\r\n 错误提示：".$ex->getMessage());
            throw new BusinessException(StatusCode::ERR_EXCEPTION,
                $ex->getMessage());
        }
        if ( ! $res) {
            Db::rollBack();
            $this->logger->info('订单插入失败,'.__LINE__.'行：'.json_encode($insert,
                    JSON_UNESCAPED_UNICODE)."\r\n");
            throw new BusinessException(StatusCode::ERR_EXCEPTION,
                '订单提交失败，稍后再试！');
        }
    }

    /**
     * 生成唯一订单号
     * GeneratOrderNumber
     * @return string
     * author MengShuai <133814250@qq.com>
     * date 2020/12/26 09:25
     */
    private function GeneratOrderNumber() : string
    {
        $order_id = date("YmdHi") . uniqid();
        if(Db::table('product_order')->where('order_id', $order_id)->exists()){
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
        var_export($product);
        if ($product->age_limit !== 'null') {
            $product->age_limit = json_decode($product->age_limit, true);
            $age = getIdCardAge((string)$validation['sim_identity']);
            if ($age < $product->age_limit[0] || $age > $product->age_limit[1]) {
                return '年龄需在'.$product->age_limit[0].'至'.$product->age_limit[1]
                    .'才能申请！'.$age . '--'.(string)$validation['sim_identity'];
            }
        }
        if ($product->pay_limit !== 'null') {
            $product->pay_limit = json_decode($product->pay_limit, true);
            //TODO  下单数量限制
        }
        if ($product->stocks < 1) {
            return $product->name.'太火爆啦，已经没有库存啦，联系客服试试吧~';
        }

        //TODO
        //后续增加禁区、代理商提货权限效验

        return true;
    }

    /**
     * saveAttachment
     * 处理回调，保存附件信息
     * User：YM
     * Date：2020/2/7
     * Time：下午6:31
     *
     * @param $inputData
     *
     * @return array
     */
    public function saveAttachment($inputData)
    {
        if ( ! isset($inputData['aid'])) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '附件ID错误');
        }
        $id = $inputData['aid'];
        $fileInfo = pathinfo($inputData['filename']);
        $result = [
            'state' => 'FAIL',
            'id'    => 0,
        ];
        if ($fileInfo) {
            $fileType = @stristr($inputData['mimeType'], "image");
            $size = $inputData['size'] ?? 0;
            $filePath = '/'.$inputData['filename'];
            $saveData = [
                'id'       => $id,
                'title'    => $fileInfo['filename'],
                'filename' => $fileInfo['basename'],
                'path'     => $filePath,
                'type'     => $fileType,
                'size'     => $size,
            ];
            $this->attachmentService->saveAttachment($saveData);
            $result['state'] = 'SUCCESS';
            $result['id'] = $id;
            $result['title'] = $fileInfo['filename'];
            $result['size'] = $size;
            $result['type'] = $fileInfo['extension'];
            $result['full_path'] =
                $this->attachmentService->getAttachmentFullUrl($filePath);
            $result['path'] = $filePath;
            $result['original'] = $fileInfo['filename'];
            return [$result];
        } else {
            return [$result];
        }
    }

}