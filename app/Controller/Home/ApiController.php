<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Constants\ProductOrderCode;
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
use App\Middleware\SpreadMiddleware;
use Hyperf\HttpServer\Annotation\Middleware;

/**
 * ApiController
 * 前台接口通讯
 *
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
    private $validationFactory;

    /**
     * @Inject()
     * @var Redis
     */
    private $Redis;

    /**
     *
     * @Inject()
     * @var BkApi
     */
    private $BkApi;

    /**
     * order_query
     * 订单查询
     * @RequestMapping(path="order_query")
     *
     * Middleware(SpreadMiddleware::class)
     */
    public function order_query()
    {
        $reqParam = $this->request->all();
        if ( ! isset($reqParam['phone'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '请输入手机号码');
        }
        if ( ! isMobileNum($reqParam['phone'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '请输入正确的手机号码');
        }
        $lists = Db::table('product_order')
            ->select('product_sale.name', 'product_sale.titile',
                'product_sale.icon', 'product_order.price', 'product_order.order_id',
                'product_order.status', 'product_order.created_at')
            ->join('product_sale', 'product_sale.id', '=', 'product_order.sid')
            ->where(['product_order.phone' => $reqParam['phone']])
            ->get();
        if ($lists === null) {
            return $this->error(StatusCode::ERR_EXCEPTION,
                '没到查到历史订单，请检查手机号是否输入正确。');
        }
        var_export($lists);
        $lists = $lists->toArray();
        foreach ($lists as $k => $v) {
            $lists[$k] = (array)$v;
            $lists[$k]['status'] = ProductOrderCode::getMessage($v->status);
        }
        unset($v);
        return $this->success($lists, '操作成功');
    }

    /**
     * uniform
     * 提交订单
     * @RequestMapping(path="uniform")
     *
     * @Middleware(SpreadMiddleware::class)
     * @property \Core\Repositories\Home\OrderSubmitRepository $orderSubmitRepository
     */
    public function uniform()
    {
        $reqParam = $this->request->all();
        if (!isset($reqParam['template'])) {
            return $this->error(StatusCode::ERR_EXCEPTION, '模板未录入');
        }
        switch ($reqParam['template']) {
            case 'default':
                $validator = $this->validationFactory->make(
                    $reqParam,
                    [
                        'sid'                    => 'required',
                        'job_number'             => 'required',
                        'certInfo.certName'      => 'required|string',
                        'certInfo.certId'        => 'required|string',
                        'certInfo.contractPhone' => 'required|string',
                        'postInfo.webProvince'   => 'required|string',
                        'postInfo.webCity'       => 'required|string',
                        'postInfo.webCounty'     => 'required|string',
                        'postInfo.address'       => 'required|string',
                    ],
                    [
                        'sid.required'                  => '商品id不能为空',
                        'job_number.required'           => '推广工号不能为空',
                        'certInfo.certName.required'    => '收货人姓名不能为空',
                        'postInfo.webProvinc.requirede' => '收货省不能为空',
                    ]
                );
                if ($validator->fails()) {
                    return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
                }
                $OrderThreeInspect = OrderThreeInspect((string)$reqParam['certInfo']['certName'],
                    (string)$reqParam['certInfo']['certId'], (string)$reqParam['certInfo']['contractPhone']);
                if ($OrderThreeInspect !== true) {
                    return $this->error(StatusCode::ERR_EXCEPTION, $OrderThreeInspect);
                }
                $res = $this->OrderSubmitRepository->default($reqParam);
                return $res;
            case "str2":
                return $this->error(StatusCode::ERR_EXCEPTION, '商品不支持选号1');
            default:
                return $this->error(StatusCode::ERR_EXCEPTION, '商品不支持选号');
        }
    }

    /**
     * selectPhones
     * 用户选号接口
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="selectPhones")
     *
     * Middleware(OssCallbackMiddleware::class)
     */
    public function selectPhones()
    {
        $params    = $this->request->all();
        $validator = $this->validationFactory->make(
            $params,
            [
                'sid'      => 'required',
                'province' => 'required|string',
                'city'     => 'required|string',
            ],
            [
                'sid.required'      => '商品id不能为空',
                'province.required' => '归属省不能为空',
                'city.required'     => '归属市不能为空',
                'province.string'   => '归属省格式错误',
                'city.string'       => '归属市格式错误',
            ]
        );

        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }

        $product = Db::table('product_sale')
            ->select('product_access.label', 'product_access.api_model', 'product_access.*')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $params['sid']])
            ->first();
        if ($product == null) {
            return $this->error(StatusCode::ERR_EXCEPTION, '商品不存在或已停售');
        }

        switch ($product->api_model) {
            case 'BkApi':
                $num    = (isset($params['num']) && $params['num'] == "10") ? "10" : "100";
                $region = $this->BkApi->getAscriptionCode((int)$params['province'], (int)$params['city']);
                if (!$region) {
                    return $this->error(StatusCode::ERR_EXCEPTION, '获取接口中归属地信息失败，请联系管理员处理');
                }
                $data = [
                    'num'              => $num,
                    'province'         => $region['province_name'],
                    'city'             => $region['city_name'],
                    'productCode'      => $product->kind,
                    'development_code' => $this->BkApi->config['development_code'],
                ];
                if (isset($params['searchNumber']) && $params['searchNumber'] != '') {
                    if (!is_numeric($params['searchNumber'])) {
                        return $this->error(StatusCode::ERR_EXCEPTION, '只能搜索数字，请重新输入');
                    }
                    $data = array_merge($data,
                        ['searchNumber' => $params['searchNumber']]);
                }

                $key = RedisCode::SELECT_PHONES . buildStringHash(json_encode($data));
                if ($res = $this->Redis->get($key)) {
                    $res = json_decode($res, true);
                    shuffle($res['data']['flexData']);
                    return $res;
                }

                $res = $this->BkApi->request('selectPhones', $data);
                if ($res['code'] == StatusCode::SUCCESS && !empty($res['data'])) {
                    $this->Redis->set($key, json_encode($res), 60);
                }
                return $res;
            case "str2":
                return $this->error(StatusCode::ERR_EXCEPTION, '商品不支持选号1');
            default:
                return $this->error(StatusCode::ERR_EXCEPTION, '商品不支持选号');
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
            'data' => json_encode(['phone' => $this->decrypt->phone]),
        ];

        $res = $this->api->request('activeMsg', $data);
        return $res;
    }
}