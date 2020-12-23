<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * SettingController.php
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午5:52
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * SettingController
 * 基础配置控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午5:52
 *
 * @Controller(prefix="admin_api/setting")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(AdminAuthMiddleware::class)
 * })
 *
 * @property \Core\Repositories\Admin\SettingRepository $settingRepo
 */
class SettingController extends BaseController
{
    /**
     * siteSet
     * 获取站点信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:55
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="site_set")
     */
    public function siteSet()
    {
        $info = $this->settingRepo->getSiteInfo();
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * siteSave
     * 保存站点设置信息
     * User：YM
     * Date：2020/2/5
     * Time：下午5:55
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="site_save")
     */
    public function siteSave()
    {
        $reqParam = $this->request->all();
        $this->settingRepo->saveSettingInfo($reqParam);

        return $this->success('ok');
    }
}