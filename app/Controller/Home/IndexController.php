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


/**
 * IndexController
 * 通知控制器
 * 接收处理三方通知
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/index")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 */
class IndexController extends BaseController
{



    /**
     * index
     * 微信扫码设备
     * @RequestMapping(path="index")
     *
     * Middleware(OssCallbackMiddleware::class)
     */

    public function index()
    {
        $res = Db::connection('bufan')->table('withdraw')
//        ->where(['status' => 1])
//        ->orderBy('product_classify.sort', 'DESC')
        ->get();
        var_export($res);
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