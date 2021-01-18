<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Core\Common\Extend\Epay\EpaySevice;
use App\Models\AdminMoneyRecharge;
use App\Constants\AdminMoneyRechargeCode;

/**
 * PaymentController
 * 支付业务处理
 *
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/payment")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 * @property AdminMoneyRecharge $AdminMoneyRechargeModel
 */
class PaymentController extends BaseController
{

    /**
     *
     * @Inject()
     * @var AdminMoneyRecharge
     */
    private $AdminMoneyRechargeModel;


    /**
     * 后台余额充值回调
     * admin_money_recharge
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 20:27
     *
     * @RequestMapping(path="admin_money_recharge_notify")
     */
    public function admin_money_recharge_notify(ResponseInterface $response): Psr7ResponseInterface
    {
        //money=1&name=余额充值&out_trade_no=202101182025YhqBR8jG&pid=28100&trade_no=2021011820250470734&trade_status=TRADE_SUCCESS&type=alipay&sign=de5af0f6ff27662b6241141cffb69cad&sign_type=MD5
        $reqParam  = $this->request->all();
        $validator = $this->validation->make(
            $reqParam,
            [
                'money'        => 'required',
                'out_trade_no' => 'required',
                'trade_no'     => 'required',
                'trade_status' => 'required',
                'type'         => 'required',
                'sign_type'    => 'required',
                'name'         => 'required',
            ],
            [
                'money.required'         => '支付金额不能为空',
                'out_trade_no.required'  => 'out_trade_no不能为空',
                'trade_no.required'      => 'trade_no不能为空',
                'trade_status.requirede' => 'trade_status不能为空',
            ]
        );
        if ($validator->fails()) {
            return $response->raw($validator->errors()->first());
        }
        if(!(make(EpaySevice::class))->notify($reqParam)){
            return $response->raw('签名错误');
        }
        $orderInfo = $this->AdminMoneyRechargeModel->query()->where(['orderid' => $reqParam['out_trade_no']])->first();
        if(!$orderInfo){
            return $response->raw('订单不存在');
        }
        if($orderInfo->status !== AdminMoneyRechargeCode::PAYMENT_STATUS_UNPAID)
        {
            return $response->raw('订单已被处理');
        }
        $this->AdminMoneyRechargeModel->notifyHandle($orderInfo,$reqParam);

        return $response->raw('success');
    }

}