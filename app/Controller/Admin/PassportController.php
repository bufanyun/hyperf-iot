<?php

declare(strict_types=1);


namespace App\Controller\Admin;

use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use App\Exception\BusinessException;
use App\Constants\StatusCode;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * PassportController
 * 通行证相关
 *
 * @package App\Controller\Admin
 *
 * @Controller(prefix="/admin_api/passport")
 *
 * @property \Core\Repositories\Admin\PassportRepository $passportRepo
 */
class PassportController extends BaseController
{

    /**
     * 登陆处理
     * login
     *
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/19 19:51
     * @PostMapping(path="login")
     */
    public function login()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'account'  => 'required',
                'password' => 'required',
            ],
            [
                'account.required'  => '账号不能为空',
                'password.required' => '密码不能为空',
            ]
        );
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER, $errorMessage);
        }
        $data = $this->passportRepo->handleLogin($inputData);
        return $this->success($data, is_string($data) ? $data : '登录成功',);
    }

    /**
     * 退出登陆
     * submitLogout
     * @return \Psr\Http\Message\ResponseInterface
     * author MengShuai <133814250@qq.com>
     * date 2021/01/19 19:52
     * @PostMapping(path="logout")
     */
    public function submitLogout()
    {
        $this->passportRepo->handleLogout();

        return $this->success('ok');
    }
}