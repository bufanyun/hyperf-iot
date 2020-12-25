<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Constants\RedisCode;
use App\Constants\StatusCode;
use App\Controller\BaseController;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\SpreadMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

/**
 * SpreadController
 * 推广下单
 * 推广页面的接口提供
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/spread")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 */
class SpreadController extends BaseController
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * pool
     * 号卡汇总列表
     * @RequestMapping(path="pool")
     *
     * @Middleware(SpreadMiddleware::class)
     */
    public function pool()
    {
        $classifys = Db::table('product_classify')
            ->where(['status' => 1])
            ->orderBy('product_classify.sort', 'DESC')
            ->get();
        $sales = Db::table('product_sale')->where(['status' => 1, 'pid' => 0])
            ->orderBy('product_sale.sort', 'DESC')
            ->get();
        return $this->view([
            'job_number' => $this->request->input('job_number'),
            'sales' => $sales,
            'classifys' => $classifys,
        ]);
    }

    /**
     * product_show
     * 产品展示页
     * @RequestMapping(path="product_show")
     *
     * @Middleware(SpreadMiddleware::class)
     */
    public function product_show()
    {
        $reqParam = $this->request->all();
        $product = Db::table('product_sale')
            ->select('product_access.label', 'product_access.*')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $reqParam['sid']])
            ->first();
        if($product == null){
            return $this->error(StatusCode::ERR_EXCEPTION,'商品不存在');
        }
        return $this->view([
            'product' => $product,
            'reqParam' => (object)$reqParam,
        ], '/Home/Spread/product_show/' . $product->label);
    }
    /**
     * plat_apply
     * 资料填写
     * @RequestMapping(path="plat_apply")
     *
     * @Middleware(SpreadMiddleware::class)
     */
    public function plat_apply()
    {
        $reqParam = $this->request->all();
        $product = Db::table('product_sale')
            ->select('product_access.label','product_access.captcha_switch','product_access.area_switch','product_access.num_select_switch',
                'product_sale.id','product_sale.name','product_sale.titile','product_sale.price','product_sale.icon','product_sale.first_desc','product_sale.recommend')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $reqParam['sid']])
            ->first();
        if($product == null){
           return $this->error(StatusCode::ERR_EXCEPTION,'商品不存在');
        }
        return $this->view(['product' => $product, 'reqParam' => (object)$reqParam]);
    }

    /**
     * place_order
     * 提交订单
     * @RequestMapping(path="place_order")
     *
     * @Middleware(SpreadMiddleware::class)
     * @property \Core\Repositories\Home\OrderSubmitRepository $orderSubmitRepository
     */
    public function place_order()
    {
        $reqParam = $this->request->all();
        if(!isset($reqParam['template'])){
            return $this->error(StatusCode::ERR_EXCEPTION, '模板未录入');
        }
        switch ($reqParam['template']){
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
                        'sid.required'               => '商品id不能为空',
                        'job_number.required'        => '推广工号不能为空',
                        'certInfo.certName.required' => '收货人姓名不能为空',
                        'postInfo.webProvinc.requirede'   => '收货省不能为空',
                    ]
                );
                if ($validator->fails()){
                    return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
                }
                $OrderThreeInspect = OrderThreeInspect((string)$reqParam['certInfo']['certName'], (string)$reqParam['certInfo']['certId'], (string)$reqParam['certInfo']['contractPhone']);
                if($OrderThreeInspect !== true){
                    return $this->error(StatusCode::ERR_EXCEPTION, $OrderThreeInspect);
                }
                $res = $this->OrderSubmitRepository->default($reqParam);
                return $res;
            case "str2":
                return $this->error(StatusCode::ERR_EXCEPTION,'商品不支持选号1');
            default:
                return $this->error(StatusCode::ERR_EXCEPTION,'商品不支持选号');
        }
    }

    /**
     * com_collection_announcement
     * 联通信息采集公告
     * @RequestMapping(path="com-collection-announcement")
     *
     * Middleware(SpreadMiddleware::class)
     */
    public function com_collection_announcement()
    {
        return $this->view([],'/Home/common/com-collection-announcement');
    }


    /**
     * suc
     * 提交成功
     * @RequestMapping(path="suc")
     *
     * Middleware(SpreadMiddleware::class)
     */
    public function suc()
    {
        return $this->view(['name' => 'ms']);
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