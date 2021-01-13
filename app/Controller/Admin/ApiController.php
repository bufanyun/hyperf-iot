<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Models\User;
use Hyperf\Di\Annotation\Inject;
use Core\Plugins\Sms;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

/**
 * UserController
 * 用户管理
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午4:04
 *
 * @Controller(prefix="/admin_api/api")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\UserRepository $userRepo
 */
class ApiController extends BaseController
{
    /**
     *
     * @Inject()
     * @var User
     */
    private $model;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    private $validationFactory;

    /**
     * @Inject()
     * @var Sms
     */
    protected $Sms;

    /**
     * send_sms
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @RequestMapping(path="send_sms")
     */
    public function send_sms()
    {
        $reqParam  = $this->request->all();
//        var_export(['$this->validationFactory' => $this->validationFactory, '$this->Sms' =>$this->Sms, 'model' =>$this->model]);
        $validator = $this->validationFactory->make(
            $reqParam,
            [
                'mobile' => 'required|/^1[34578]\d{9}$/',
                'event'  => 'required',
            ],
            [
                'mobile.required' => '手机号不能为空',
                'event.required'  => '事件类型不能为空',

            ]
        );
        if ($validator->fails()) {
            return $this->error(StatusCode::ERR_EXCEPTION, $validator->errors()->first());
        }

        var_export($reqParam);
//        $res = $this->Sms->send($reqParam['mobile'], $code = null, $reqParam['event']);
//        if(!$res){
//            return $this->error(StatusCode::ERR_EXCEPTION, '发送失败，稍后再试！');
//        }
    }


    public function check_sms()
    {

    }
}