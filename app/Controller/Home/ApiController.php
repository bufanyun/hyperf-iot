<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\OssCallbackMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Di\Annotation\Inject;
use Core\Common\Extend\CardApi\Bk\Tools as BkApi;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Core\Common\Container\Redis;

/**
 * ApiController
 * 前台接口通讯
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/api")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 */
class ApiController extends BaseController
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * @Inject()
     * @var Redis
     */
    protected $Redis;

    /**
     *
     * @Inject()
     * @var BkApi
     */
    private $BkApi;


    /**
     * 统一下单接口
     * @return mixed
     */
    public function uniform()
    {
        $region = $this->districtInspection->getAscription((string)$this->decrypt->province, (string)$this->decrypt->city);
        if(!$region){
            throw new BadRequestException('获取接口中归属地信息失败，请联系管理员处理', 71);
        }
        $area = $this->districtInspection->getArea((string)$this->decrypt->postProvince, (string)$this->decrypt->postCity, (string)$this->decrypt->postDistrict);
        if(!$area){
            throw new BadRequestException('获取接口中收货地信息失败，请联系管理员处理', 72);
        }
        if (is_phone($this->decrypt->receiverPhone) === false) {
            throw new BadRequestException(\PhalApi\T('收货人手机号不正确！'), 73);
        }
        if (isset($this->decrypt->newnumber) && is_phone($this->decrypt->newnumber) === false) {
            throw new BadRequestException(\PhalApi\T('选择的号码格式不正确，请检查！'), 74);
        }
        if (is_idcard($this->decrypt->certId) === false){
            throw new BadRequestException(\PhalApi\T('收货人身份证号不正确！'), 75);
        }
        $admin_id = isset($this->decrypt->new_admin_id) && $this->decrypt->new_admin_id>0 ? $this->decrypt->new_admin_id : $this->decrypt->admin_id;
        $AdminExtraModel = new AdminExtraModel;
        if ($AdminExtraModel->getInfo($admin_id) == null){
            throw new BadRequestException(\PhalApi\T('绑定管理员ID不存在，请检查！'), 76);
        }

        $data = [
            'name'             => $this->decrypt->certName,
            'identity'         => $this->decrypt->certId,
            'contact'          => $this->decrypt->receiverPhone,
            'ship_province'    => $area['province_name'],
            'ship_city'        => $area['city_name'],
            'ship_country'     => $area['district_name'],
            'ship_addr'        => $this->decrypt->postAddress,
            'province'         => $region['province_name'],
            'city'             => $region['city_name'],
            'productCode'      => $this->decrypt->productCode,
            'captchaId'        => $this->decrypt->captchaId,
            'development_code' => $this->api->config['development_code'],
        ];
        if (isset($this->decrypt->newnumber)){
            $data = array_merge($data, ['newnumber' => $this->decrypt->newnumber]);
        }

        $res = $this->api->request('ZOPsubmit', $data);
        return $res;
    }

    /**
     * selectPhones
     * 用户选号接口
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="selectPhones")
     *
     * Middleware(OssCallbackMiddleware::class)
     */
    public function selectPhones()
    {
        $params = $this->request->all();
        $validator = $this->validationFactory->make(
            $params,
            [
                'sid' => 'required',
                'province' => 'required|string',
                'city' => 'required|string',
            ],
            [
                'sid.required' => '商品id不能为空',
                'province.required' => '归属省不能为空',
                'city.required' => '归属市不能为空',
                'province.string' => '归属省格式错误',
                'city.string' => '归属市格式错误',
            ]
        );

        if ($validator->fails()){
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }

        $product = Db::table('product_sale')
            ->select('product_access.label', 'product_access.api_model', 'product_access.*')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $params['sid']])
            ->first();
        if($product == null){
            return $this->error(StatusCode::ERR_EXCEPTION,'商品不存在或已停售');
        }

        switch ($product->api_model){
            case 'BkApi':
                $num = (isset($params['num']) && $params['num'] == "10") ? "10" : "100";
                $region = $this->BkApi->getAscription($params['province'], $params['city']);
                if(!$region){
                    return $this->error(StatusCode::ERR_EXCEPTION, '获取接口中归属地信息失败，请联系管理员处理');
                }
                $data = [
                    'num'              => $num,
                    'province'         => $region['province_name'],
                    'city'             => $region['city_name'],
                    'productCode'      => $product->kind,
                    'development_code' => $this->BkApi->config['development_code'],
                ];
                if (isset($params['searchNumber'])) {
                    $data = array_merge($data,
                        ['searchNumber' => $params['searchNumber']]);
                }

                $key = RedisCode::SELECT_PHONES . buildStringHash(json_encode($data));
                if($res = $this->Redis->get($key)){
                    $res = json_decode($res,true);
                    shuffle($res['data']['flexData']);
                    return $res;
                }

                $res = $this->BkApi->request('selectPhones', $data);
                if($res['code'] == StatusCode::SUCCESS && !empty($res['data'])){
                    $this->Redis->set($key, json_encode($res), 60);
                }
                return $res;
            case "str2":
                return $this->error(StatusCode::ERR_EXCEPTION,'商品不支持选号1');
            default:
                return $this->error(StatusCode::ERR_EXCEPTION,'商品不支持选号');
            }
    }
    /**
     * Bk联通获取验证码
     */
    public function getCode()
    {
        $data = [
            'identity'         => $this->decrypt->identity,
            'contact'          => $this->decrypt->contact,
            'development_code' => $this->api->config['development_code'],
        ];

        $res = $this->api->request('getCode', $data);
        return $res;
    }

    /**
     * Bk联通效验验证码
     */
    public function messageCheck()
    {
        $data = [
            'captcha'          => $this->decrypt->captcha,
            'identity'         => $this->decrypt->identity,
            'contact'          => $this->decrypt->contact,
            'development_code' => $this->api->config['development_code'],
        ];

        $res = $this->api->request('messageCheck', $data);
        return $res;
    }

    /**
     * 订单明细查询接口
     */
    public function GetOrders()
    {
        $data = [
            'startDate'        => $this->decrypt->startDate,
            'endDate'          => $this->decrypt->endDate,
            'attractDevelopId' => $this->api->config['development_code'],
        ];

        $res = $this->api->request('GetOrders', $data);
        return $res;
    }

    /**
     * 获取号码激活状态
     */
    public function activeMsg()
    {
        $data = [
            'data'        => json_encode(['phone' => $this->decrypt->phone]),
        ];

        $res = $this->api->request('activeMsg', $data);
        return $res;
    }

    /**
     * test
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="test")
     *
     * Middleware(OssCallbackMiddleware::class)
     */

    public function test()
    {
        $method = $this->request->input('method', 'AgentTrade/checkOrder', false);
        $params = [
            'method' => !$method ? 'AgentTrade/checkOrder' : $method,
            'data'   => json_encode([
                'test' => 1,
            ]),
        ];  //订单扫描

        if($method == 'AgentTrade/addOrder') {
            $params = [
                'method' => $method,
                'data'  => json_encode([
                    'client' => 'system',
                    'source' => 'tmt',
                    'price'  => $this->request->input('price', '0.01'),
                    'type'   => $this->request->input('type', '0'),
                    'qrurl'  => 'https://qr.alipay.com/fkx10920stvogcastck55c5?t=1604627435932', //小辉
                    'orderid'   => uniqid(),
                    'device_id' => '868019047358743', //小米7 -2
                ]),
            ];
        }

        $sign = config('payment_sign');
        $host = 'ws://127.0.0.1:1888?' . $sign['key']  . "=" . encrypt($params, $sign['encryption']);
        $client = $this->clientFactory->create($host);
        /** @var Frame $msg */
        $msg = $client->recv(3)->data ?? null;
        if ($msg == null){
            var_export(['$msg' => $msg]);
            return $this->error(StatusCode::ERR_EXCEPTION,'获取信息失败-1');
        }
        $result = json_decode($msg, true);
        if (isset($result['code']) && $result['code'] == 0) {
            return $this->success($result['data'], $result['msg']);
        }
        return $this->error(StatusCode::ERR_EXCEPTION, isset($result['msg']) ?? '获取信息失败-2');
    }

}