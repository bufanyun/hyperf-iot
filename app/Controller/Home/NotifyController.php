<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * NotifyController.php
 *
 * User：YM
 * Date：2020/2/7
 * Time：下午6:21
 */


namespace App\Controller\Home;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\OssCallbackMiddleware;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\ApplicationContext;

/**
 * NotifyController
 * 通知控制器
 * 接收处理三方通知
 * @package App\Controller\Home
 * User：YM
 * Date：2020/2/7
 * Time：下午6:21
 *
 * @Controller(prefix="public11/notify")
 *
 * @property \Core\Repositories\Home\AttachmentRepository $attachmentRepository
 */
class NotifyController extends BaseController
{
    /**
     * ossCallback
     * 函数的含义说明
     * User：YM
     * Date：2020/2/7
     * Time：下午6:24
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="oss_callback")
     *
     * Middleware(OssCallbackMiddleware::class)
     */

    public function ossCallback()
    {

        $row = Db::table('ip_region')
//            ->limit(1000)
            ->get()->toArray();

        $container = ApplicationContext::getContainer();
        $redis = $container->get(\Hyperf\Redis\Redis::class);
        $redis->set('qwe', 0);

        return $this->response->json(['n' => $row]);


//        return $row;

//        return $this->error('666');
//
//        $body = $this->request->getBody();
//        if($body){
//            $content = $body->getContents();
//            parse_str($content, $inputData);
//            $ret = $this->attachmentRepository->saveAttachment($inputData);
//            return $this->success($ret);
//        }else{
//            $data = ["state" => "fail"];
//            return $this->error($data);
//        }
    }


    /**
     * ossCallback2
     * 函数的含义说明
     * User：YM
     * Date：2020/2/7
     * Time：下午6:24
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="oss_callback2")
     *
     * @Middleware(OssCallbackMiddleware::class)
     */
    public function ossCallback2()
    {
        return $this->error('666');


        $body = $this->request->getBody();
        if ($body) {
            $content = $body->getContents();
            parse_str($content, $inputData);
            $ret = $this->attachmentRepository->saveAttachment($inputData);
            return $this->success($ret);
        } else {
            $data = ["state" => "fail"];
            return $this->error($data);
        }
    }
}