<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * PassportRepository.php
 *
 * 通行证仓库
 *
 * User：YM
 * Date：2020/1/7
 * Time：下午6:56
 */


namespace Core\Repositories\Admin;


use Core\Common\Container\Auth;
use Core\Repositories\BaseRepository;
use Hyperf\Di\Annotation\Inject;

/**
 * PassportRepository
 * 通行证仓库
 * @package Core\Repositories\Admin
 * User：YM
 * Date：2020/1/7
 * Time：下午6:56
 *
 * @property \Core\Services\UserService $userService
 */
class PassportRepository extends BaseRepository
{

    /**
     * @Inject()
     * @var Auth
     */
    protected $auth;

    /**
     * handleLogin
     * 处理用户登录
     * User：YM
     * Date：2020/1/10
     * Time：下午12:26
     * @param $inputData
     * @return array|bool
     */
    public function handleLogin($inputData)
    {
        
        $userInfo = $this->auth->handleLogin($inputData);

        return $userInfo;
    }

    /**
     * createUser
     * 创建用户
     * User：YM
     * Date：2020/1/10
     * Time：下午2:10
     * @param $inputData
     * @return bool
     * @throws \Exception
     */
    public function createUser($inputData)
    {
        $id = $this->userService->saveUser($inputData,true);

        return $id;
    }

    /**
     * handleLogout
     * 处理退出
     * User：YM
     * Date：2020/3/8
     * Time：下午11:35
     * @return string
     */
    public function handleLogout()
    {
        $info = $this->auth->logout();
        return $info;
    }

}