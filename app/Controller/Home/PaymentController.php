<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

/**
 * PaymentController
 * 支付业务处理
 *
 * @package App\Controller\Home
 *
 * @Controller(prefix="home/payment")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 */
class PaymentController extends BaseController
{


    /**
     * 后台余额充值回调
     * admin_money_recharge
     * author MengShuai <133814250@qq.com>
     * date 2021/01/18 20:27
     *
     *  @RequestMapping(path="admin_money_recharge_notify")
     */
    public function admin_money_recharge_notify()
    {
        //money=1&name=余额充值&out_trade_no=202101182025YhqBR8jG&pid=28100&trade_no=2021011820250470734&trade_status=TRADE_SUCCESS&type=alipay&sign=de5af0f6ff27662b6241141cffb69cad&sign_type=MD5
        
    }

    /**
     * pool
     * 号卡汇总列表
     * @RequestMapping(path="pool")
     *
     * @Middleware(PaymentMiddleware::class)
     */
    public function pool()
    {
        $reqParam  = $this->request->all();
        $classifys = Db::table('product_classify')
            ->where(['status' => 1])
            ->orderBy('product_classify.sort', 'DESC')
            ->get();
        $sales     = Db::table('product_sale')->where(['status' => 1, 'pid' => 0])
            ->orderBy('product_sale.sort', 'DESC')
            ->get();
        unset($reqParam['sid'], $reqParam['r']);
        return $this->view([
            'reqParam'  => $reqParam,
            'sales'     => $sales,
            'classifys' => $classifys,
            'routePath' => '/' . $this->request->path(),
        ]);
    }

    /**
     * product_show
     * 产品展示页
     *
     * @RequestMapping(path="product_show")
     *
     * @Middleware(PaymentMiddleware::class)
     */
    public function product_show()
    {
        $reqParam = $this->request->all();
        $product  = Db::table('product_sale')
            ->select('product_access.label', 'product_sale.*')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $reqParam['sid']])
            ->first();
        if ($product == null) {
            return $this->error(StatusCode::ERR_EXCEPTION, '商品不存在');
        }
        unset($reqParam['r']);
        return $this->view([
            'product'   => $product,
            'reqParam'  => $reqParam,
            'routePath' => '/' . $this->request->path(),
        ], '/Home/Payment/product_show/' . $product->label);
    }

    /**
     * plat_apply
     * 资料填写
     *
     * @RequestMapping(path="plat_apply")
     *
     * @Middleware(PaymentMiddleware::class)
     */
    public function plat_apply()
    {
        $reqParam = $this->request->all();
        $product  = Db::table('product_sale')
            ->select('product_access.label', 'product_access.captcha_switch', 'product_access.area_switch',
                'product_access.num_select_switch',
                'product_sale.id', 'product_sale.name', 'product_sale.titile', 'product_sale.price',
                'product_sale.icon', 'product_sale.first_desc', 'product_sale.recommend', 'product_sale.cid')
            ->join('product_access', 'product_access.id', '=', 'product_sale.access')
            ->where(['product_sale.status' => 1, 'product_sale.id' => $reqParam['sid']])
            ->first();
        if ($product == null) {
            return $this->error(StatusCode::ERR_EXCEPTION, '商品不存在');
        }
        unset($reqParam['r']);
        return $this->view([
            'product'         => $product,
            'reqParam'        => (object)$reqParam,
            'routePath'       => '/' . $this->request->path(),
            'interfaceDomain' => isset($this->request->getHeaders()['host'][0]) ? 'http://' . $this->request->getHeaders()['host'][0] : env('API_HOME_INTERFACE'),
        ]);
    }

    /**
     * com_collection_announcement
     * 联通信息采集公告
     *
     * @RequestMapping(path="com-collection-announcement")
     *
     * Middleware(PaymentMiddleware::class)
     */
    public function com_collection_announcement()
    {
        return $this->view([], '/Home/common/com-collection-announcement');
    }

    /**
     * tel_collection_announcement
     * 联通信息采集公告
     *
     * @RequestMapping(path="tel-collection-announcement")
     *
     * Middleware(PaymentMiddleware::class)
     */
    public function tel_collection_announcement()
    {
        return $this->view([], '/Home/common/tel-collection-announcement');
    }

    /**
     * suc
     * 提交成功
     *
     * @RequestMapping(path="suc")
     *
     * Middleware(PaymentMiddleware::class)
     */
    public function suc()
    {
        return $this->view(['name' => 'ms']);
    }

}